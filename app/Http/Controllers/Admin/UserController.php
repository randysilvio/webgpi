<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Klasis; 
use App\Models\Jemaat; 
use App\Models\Pegawai; 
use App\Models\JenisWadahKategorial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role; 
use Illuminate\Validation\Rules; 
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
        // Terapkan perlindungan Super Admin ke semua fungsi, KECUALI fungsi berhenti menyamar
        $this->middleware('role:Super Admin')->except(['stopImpersonate']);
    }

    public function index(Request $request)
    {
        $query = User::with(['roles', 'klasisTugas', 'jemaatTugas', 'pegawai', 'jenisWadah'])->latest();

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

    public function create()
    {
        $roles = Role::all(); // Object
        $pegawais = Pegawai::orderBy('nama_lengkap', 'asc')->get();
        $klasisList = Klasis::orderBy('nama_klasis', 'asc')->get();
        $jemaatList = Jemaat::orderBy('nama_jemaat', 'asc')->get(); // Load semua untuk create
        $jenisWadahs = JenisWadahKategorial::orderBy('nama_wadah', 'asc')->get();

        return view('admin.user.create', compact('roles', 'pegawais', 'klasisList', 'jemaatList', 'jenisWadahs'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'roles' => ['required', 'array'],
            'roles.*' => ['string', Rule::exists('roles', 'name')],
            'pegawai_id' => ['nullable', 'exists:pegawai,id', 'unique:'.User::class.',pegawai_id'],
            'klasis_id' => ['nullable', 'exists:klasis,id'],
            'jemaat_id' => ['nullable', 'exists:jemaat,id'],
            'jenis_wadah_id' => ['nullable', 'exists:jenis_wadah_kategorial,id'],
        ]);

        try {
            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
                'pegawai_id' => $validatedData['pegawai_id'] ?? null,
                'klasis_id' => $validatedData['klasis_id'] ?? null,
                'jemaat_id' => $validatedData['jemaat_id'] ?? null,
                'jenis_wadah_id' => $validatedData['jenis_wadah_id'] ?? null,
            ]);

            $rolesToSync = $validatedData['roles'];
            // Proteksi Super Admin (Opsional jika user login bukan ID 1)
            if (Auth::check() && Auth::id() != 1) {
                $rolesToSync = array_filter($rolesToSync, fn($r) => $r !== 'Super Admin');
            }
            $user->assignRole($rolesToSync);

            return redirect()->route('admin.users.index')->with('success', 'User baru berhasil dibuat.');

        } catch (\Exception $e) {
             Log::error('Gagal buat user: ' . $e->getMessage());
             return back()->with('error', 'Gagal: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $user = User::with(['roles', 'klasisTugas', 'jemaatTugas', 'pegawai', 'jenisWadah'])->findOrFail($id);
        return view('admin.user.show', compact('user'));
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);

        $roles = Role::all();
        $pegawais = Pegawai::orderBy('nama_lengkap', 'asc')->get();
        $klasisList = Klasis::orderBy('nama_klasis', 'asc')->get();
        $jenisWadahs = JenisWadahKategorial::orderBy('nama_wadah', 'asc')->get();
        
        $jemaatList = collect();
        if ($user->klasis_id) {
            $jemaatList = Jemaat::where('klasis_id', $user->klasis_id)->orderBy('nama_jemaat')->get();
        }

        $userRoles = $user->roles->pluck('name')->toArray();

        return view('admin.user.edit', compact(
            'user', 'roles', 'pegawais', 'klasisList', 'jenisWadahs', 'jemaatList', 'userRoles'
        ));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$id],
            'roles' => ['required', 'array'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'pegawai_id' => ['nullable', 'exists:pegawai,id'], 
            'klasis_id' => ['nullable', 'exists:klasis,id'],
            'jemaat_id' => ['nullable', 'exists:jemaat,id'],
            'jenis_wadah_id' => ['nullable', 'exists:jenis_wadah_kategorial,id'],
        ]);

        try {
            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'pegawai_id' => $request->pegawai_id,
                'klasis_id' => $request->klasis_id,
                'jemaat_id' => $request->jemaat_id,
                'jenis_wadah_id' => $request->jenis_wadah_id,
            ];

            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            $user->update($data);
            $user->syncRoles($request->roles);
            
            return redirect()->route('admin.users.index')->with('success', 'User berhasil diperbarui.');

        } catch (\Exception $e) {
             return back()->with('error', 'Gagal update: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        if ($id == 1) return back()->with('error', 'Super Admin Utama tidak bisa dihapus.');
        User::destroy($id);
        return redirect()->route('admin.users.index')->with('success', 'User dihapus.');
    }

    /**
     * Memulai Mode Menyamar
     */
    public function impersonate($id)
    {
        $targetUser = User::findOrFail($id);

        // Keamanan: Tidak bisa menyamar jadi diri sendiri atau sesama Super Admin
        if ($id == Auth::id() || $targetUser->hasRole('Super Admin')) {
            return back()->with('error', 'Tidak bisa menyamar sebagai Super Admin lain atau diri sendiri.');
        }

        // Simpan ID asli (Super Admin) ke dalam session
        session()->put('impersonate_by', Auth::id());

        // Login paksa sebagai target
        Auth::loginUsingId($id);

        return redirect()->route('admin.dashboard')->with('success', 'Mode Menyamar Aktif: Anda sekarang beroperasi sebagai ' . $targetUser->name);
    }

    /**
     * Menghentikan Mode Menyamar dan kembali ke Super Admin
     */
    public function stopImpersonate()
    {
        // Cek apakah user sedang dalam mode menyamar
        if (session()->has('impersonate_by')) {
            $originalUserId = session()->pull('impersonate_by');
            
            // Login kembali sebagai Super Admin
            Auth::loginUsingId($originalUserId);
            
            return redirect()->route('admin.users.index')->with('success', 'Penyamaran dihentikan. Anda telah kembali sebagai admin.');
        }

        return redirect()->route('admin.dashboard');
    }
}