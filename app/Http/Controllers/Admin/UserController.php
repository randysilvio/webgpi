<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Klasis; 
use App\Models\Jemaat; 
use App\Models\Pendeta;
use App\Models\JenisWadahKategorial; // <-- Import Model Wadah (BARU)
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role; 
use Illuminate\Validation\Rules; 
use Illuminate\Validation\Rule; 

class UserController extends Controller
{
    // Middleware
    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware('role:Super Admin'); // Hanya Super Admin yang boleh kelola user
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Eager load relasi termasuk jenisWadah
        $query = User::with(['roles', 'klasisTugas', 'jemaatTugas', 'pendeta', 'jenisWadah'])->latest();

        if ($request->filled('search')) {
             $searchTerm = '%' . $request->search . '%';
             $query->where(function($q) use ($searchTerm) {
                 $q->where('name', 'like', $searchTerm)
                   ->orWhere('email', 'like', $searchTerm);
             });
         }

        $users = $query->paginate(15)->appends($request->query());

        return view('admin.user.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Ambil semua nama role
        $roles = Role::pluck('name', 'name');
        // Jika user bukan ID 1 (Super Admin Utama), jangan tampilkan opsi Super Admin untuk assign ke orang lain
        if (Auth::check() && Auth::id() != 1) {
            $roles = Role::where('name', '!=', 'Super Admin')->pluck('name', 'name');
        }

        $klasisOptions = Klasis::orderBy('nama_klasis')->pluck('nama_klasis', 'id');
        $jemaatOptions = Jemaat::orderBy('nama_jemaat')->pluck('nama_jemaat', 'id'); 
        $pendetaOptions = Pendeta::orderBy('nama_lengkap')->pluck('nama_lengkap', 'id');
        
        // Ambil Data Wadah (BARU)
        $wadahs = JenisWadahKategorial::orderBy('nama_wadah')->get();

        return view('admin.user.create', compact('roles', 'klasisOptions', 'jemaatOptions', 'pendetaOptions', 'wadahs'));
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
            
            // Validasi Relasi
            'klasis_id' => ['nullable', 'exists:klasis,id'],
            'jemaat_id' => ['nullable', 'exists:jemaat,id'],
            'pendeta_id' => ['nullable', 'exists:pendeta,id', 'unique:'.User::class.',pendeta_id'],
            'jenis_wadah_id' => ['nullable', 'exists:jenis_wadah_kategorial,id'], // Validasi Wadah (BARU)
        ]);

        try {
            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
                'klasis_id' => $validatedData['klasis_id'] ?? null,
                'jemaat_id' => $validatedData['jemaat_id'] ?? null,
                'pendeta_id' => $validatedData['pendeta_id'] ?? null,
                'jenis_wadah_id' => $validatedData['jenis_wadah_id'] ?? null, // Simpan Wadah (BARU)
            ]);

            // Jangan izinkan assign Super Admin jika yang membuat bukan Super Admin
            $rolesToSync = $validatedData['roles'];
            if (Auth::check() && !Auth::user()->hasRole('Super Admin')) {
                $rolesToSync = array_filter($rolesToSync, fn($role) => $role !== 'Super Admin');
            }

            $user->syncRoles($rolesToSync);

            return redirect()->route('admin.users.index')->with('success', 'User baru berhasil dibuat.');

        } catch (\Exception $e) {
             Log::error('Gagal membuat user baru: ' . $e->getMessage());
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
        $user->load(['roles', 'klasisTugas', 'jemaatTugas', 'pendeta', 'jenisWadah']);
        return view('admin.user.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $roles = Role::pluck('name', 'name');
        if (Auth::check() && Auth::id() != 1) {
            $roles = Role::where('name', '!=', 'Super Admin')->pluck('name', 'name');
        }

        $klasisOptions = Klasis::orderBy('nama_klasis')->pluck('nama_klasis', 'id');
        $jemaatOptions = Jemaat::orderBy('nama_jemaat')->pluck('nama_jemaat', 'id');
        $pendetaOptions = Pendeta::orderBy('nama_lengkap')->pluck('nama_lengkap', 'id');
        
        // Ambil Data Wadah (BARU)
        $wadahs = JenisWadahKategorial::orderBy('nama_wadah')->get();
        
        $userRoles = $user->roles->pluck('name')->toArray();

        return view('admin.user.edit', compact('user', 'roles', 'klasisOptions', 'jemaatOptions', 'pendetaOptions', 'userRoles', 'wadahs'));
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
            
            // Validasi Relasi
            'klasis_id' => ['nullable', 'exists:klasis,id'],
            'jemaat_id' => ['nullable', 'exists:jemaat,id'],
            'pendeta_id' => ['nullable', 'exists:pendeta,id', Rule::unique('users', 'pendeta_id')->ignore($user->id)],
            'jenis_wadah_id' => ['nullable', 'exists:jenis_wadah_kategorial,id'], // Validasi Wadah (BARU)
        ]);

         try {
            $updateData = [
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'klasis_id' => $validatedData['klasis_id'] ?? null,
                'jemaat_id' => $validatedData['jemaat_id'] ?? null,
                'pendeta_id' => $validatedData['pendeta_id'] ?? null,
                'jenis_wadah_id' => $validatedData['jenis_wadah_id'] ?? null, // Update Wadah (BARU)
            ];

            if (!empty($validatedData['password'])) {
                $updateData['password'] = Hash::make($validatedData['password']);
            }

            $user->update($updateData);

            $rolesToSync = $validatedData['roles'];
            // Proteksi agar Super Admin ID 1 tidak kehilangan rolenya
            if ($user->id == 1 && !in_array('Super Admin', $rolesToSync)) {
                $rolesToSync[] = 'Super Admin'; 
            }
            // Proteksi agar user lain tidak bisa assign Super Admin
            if (Auth::check() && !Auth::user()->hasRole('Super Admin')) {
                 $rolesToSync = array_filter($rolesToSync, fn($role) => $role !== 'Super Admin');
            }

            $user->syncRoles($rolesToSync);
            
            return redirect()->route('admin.users.index')->with('success', 'Data user berhasil diperbarui.');

        } catch (\Exception $e) {
             Log::error('Gagal update user ID: ' . $user->id . '. Error: '. $e->getMessage());
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
             return redirect()->route('admin.users.index')->with('error', 'User Super Admin utama (ID 1) tidak dapat dihapus.');
        }

        try {
            $userName = $user->name;
            $user->delete();
            return redirect()->route('admin.users.index')->with('success', 'User (' . $userName . ') berhasil dihapus.');

        } catch (\Exception $e) {
             Log::error('Gagal hapus user ID: ' . $user->id . '. Error: ' . $e->getMessage());
             return redirect()->route('admin.users.index')
                              ->with('error', 'Gagal menghapus user. Error: ' . $e->getMessage());
        }
    }
}