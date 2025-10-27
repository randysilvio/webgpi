<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Klasis; // Untuk dropdown relasi
use App\Models\Jemaat; // Untuk dropdown relasi
use App\Models\Pendeta; // Untuk dropdown relasi
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role; // <-- Import model Role
use Illuminate\Validation\Rules; // <-- Untuk validasi password
use Illuminate\Validation\Rule; // <-- Untuk validasi unique

class UserController extends Controller
{
    // Middleware (AKTIFKAN SETELAH LOGIN & ROLE DISET)
    public function __construct()
    {
        // AKTIFKAN KEMBALI
        $this->middleware(['auth']);
        $this->middleware('role:Super Admin'); // Hanya Super Admin yang boleh kelola user
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::with(['roles', 'klasisTugas', 'jemaatTugas', 'pendeta'])->latest(); // Eager load relasi

        if ($request->filled('search')) {
             $searchTerm = '%' . $request->search . '%';
             $query->where(function($q) use ($searchTerm) {
                 $q->where('name', 'like', $searchTerm)
                   ->orWhere('email', 'like', $searchTerm);
             });
         }

        $users = $query->paginate(15)->appends($request->query());

        // Kembali ke 'admin.user.index' (singular)
        return view('admin.user.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Ambil semua nama role
        $roles = Role::pluck('name', 'name');
        // Jika user bukan ID 1 (Super Admin), jangan tampilkan opsi Super Admin
        if (Auth::check() && Auth::id() != 1) {
            $roles = Role::where('name', '!=', 'Super Admin')->pluck('name', 'name');
        }

        $klasisOptions = Klasis::orderBy('nama_klasis')->pluck('nama_klasis', 'id');
        $jemaatOptions = Jemaat::orderBy('nama_jemaat')->pluck('nama_jemaat', 'id'); // TODO: Filter by klasis via JS
        $pendetaOptions = Pendeta::orderBy('nama_lengkap')->pluck('nama_lengkap', 'id');

        // Kembali ke 'admin.user.create' (singular)
        return view('admin.user.create', compact('roles', 'klasisOptions', 'jemaatOptions', 'pendetaOptions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'roles' => ['required', 'array'],
            'roles.*' => ['string', Rule::exists('roles', 'name')],
            'klasis_id' => ['nullable', 'exists:klasis,id'],
            'jemaat_id' => ['nullable', 'exists:jemaat,id'],
            'pendeta_id' => ['nullable', 'exists:pendeta,id', 'unique:'.User::class.',pendeta_id'],
        ]);

        try {
            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
                'klasis_id' => $validatedData['klasis_id'] ?? null,
                'jemaat_id' => $validatedData['jemaat_id'] ?? null,
                'pendeta_id' => $validatedData['pendeta_id'] ?? null,
            ]);

            // Jangan izinkan assign Super Admin jika bukan Super Admin
            $rolesToSync = $validatedData['roles'];
            if (Auth::check() && !Auth::user()->hasRole('Super Admin')) {
                $rolesToSync = array_filter($rolesToSync, fn($role) => $role !== 'Super Admin');
            }

            $user->syncRoles($rolesToSync);

            // Kembali ke 'admin.users.index' (plural untuk route name)
            return redirect()->route('admin.users.index')->with('success', 'User baru berhasil dibuat.');

        } catch (\Exception $e) {
             Log::error('Gagal membuat user baru: ' . $e->getMessage());
              // Kembali ke 'admin.users.create' (plural untuk route name)
             return redirect()->route('admin.users.create')
                              ->with('error', 'Gagal membuat user baru. Error: ' . $e->getMessage())
                              ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load(['roles', 'klasisTugas', 'jemaatTugas', 'pendeta']);
        // Kembali ke 'admin.user.show' (singular)
        return view('admin.user.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $roles = Role::pluck('name', 'name');
        // Jika user bukan ID 1, jangan tampilkan opsi Super Admin
        if (Auth::check() && Auth::id() != 1) {
            $roles = Role::where('name', '!=', 'Super Admin')->pluck('name', 'name');
        }

        $klasisOptions = Klasis::orderBy('nama_klasis')->pluck('nama_klasis', 'id');
        $jemaatOptions = Jemaat::orderBy('nama_jemaat')->pluck('nama_jemaat', 'id');
        $pendetaOptions = Pendeta::orderBy('nama_lengkap')->pluck('nama_lengkap', 'id');
        $userRoles = $user->roles->pluck('name')->toArray();

        // Kembali ke 'admin.user.edit' (singular)
        return view('admin.user.edit', compact('user', 'roles', 'klasisOptions', 'jemaatOptions', 'pendetaOptions', 'userRoles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
         $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'roles' => ['required', 'array'],
            'roles.*' => ['string', Rule::exists('roles', 'name')],
            'klasis_id' => ['nullable', 'exists:klasis,id'],
            'jemaat_id' => ['nullable', 'exists:jemaat,id'],
            'pendeta_id' => ['nullable', 'exists:pendeta,id', Rule::unique('users', 'pendeta_id')->ignore($user->id)],
        ]);

         try {
            $updateData = [
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'klasis_id' => $validatedData['klasis_id'] ?? null,
                'jemaat_id' => $validatedData['jemaat_id'] ?? null,
                'pendeta_id' => $validatedData['pendeta_id'] ?? null,
            ];

            if (!empty($validatedData['password'])) {
                $updateData['password'] = Hash::make($validatedData['password']);
            }

            $user->update($updateData);

            $rolesToSync = $validatedData['roles'];
            // Proteksi agar Super Admin ID 1 tidak kehilangan rolenya
            if ($user->id == 1 && !in_array('Super Admin', $rolesToSync)) {
                $rolesToSync[] = 'Super Admin'; // Paksa tetap jadi Super Admin
            }
            // Proteksi agar user lain tidak bisa assign Super Admin
            if (Auth::check() && !Auth::user()->hasRole('Super Admin')) {
                 $rolesToSync = array_filter($rolesToSync, fn($role) => $role !== 'Super Admin');
            }

            $user->syncRoles($rolesToSync);
             // Kembali ke 'admin.users.index' (plural untuk route name)
            return redirect()->route('admin.users.index')->with('success', 'Data user berhasil diperbarui.');

        } catch (\Exception $e) {
             Log::error('Gagal update user ID: ' . $user->id . '. Error: '. $e->getMessage());
              // Kembali ke 'admin.users.edit' (plural untuk route name)
             return redirect()->route('admin.users.edit', $user->id)
                              ->with('error', 'Gagal memperbarui data user. Error: ' . $e->getMessage())
                              ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if ($user->id == 1) {
             // Kembali ke 'admin.users.index' (plural untuk route name)
             return redirect()->route('admin.users.index')->with('error', 'User Super Admin utama (ID 1) tidak dapat dihapus.');
        }

        // Cek relasi sebelum hapus (opsional tapi bagus)
        // if ($user->pendeta_id) {
        //      return redirect()->route('admin.users.index')->with('error', 'User ini terhubung ke Data Pendeta. Hapus Data Pendeta terkait untuk menghapus user ini.');
        // }

        try {
            $userName = $user->name;
            $user->delete();
             // Kembali ke 'admin.users.index' (plural untuk route name)
            return redirect()->route('admin.users.index')->with('success', 'User (' . $userName . ') berhasil dihapus.');

        } catch (\Exception $e) {
             Log::error('Gagal hapus user ID: ' . $user->id . '. Error: ' . $e->getMessage());
              // Kembali ke 'admin.users.index' (plural untuk route name)
             return redirect()->route('admin.users.index')
                              ->with('error', 'Gagal menghapus user. Error: ' . $e->getMessage());
        }
    }
}