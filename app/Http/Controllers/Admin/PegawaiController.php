<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use App\Models\Klasis;
use App\Models\Jemaat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class PegawaiController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Pegawai::with(['klasis', 'jemaat', 'user']);

        // RBAC Scoping
        if ($user->hasRole('Admin Klasis') && $user->klasis_id) {
            $query->where('klasis_id', $user->klasis_id);
        } elseif ($user->hasRole('Admin Jemaat') && $user->jemaat_id) {
            $query->where('jemaat_id', $user->jemaat_id);
        }

        $statsQuery = clone $query;
        $stats = $statsQuery->reorder()->selectRaw('
            count(*) as total,
            sum(case when jenis_pegawai = "Pendeta" then 1 else 0 end) as total_pendeta,
            sum(case when jenis_pegawai != "Pendeta" then 1 else 0 end) as total_non_pendeta,
            sum(case when status_aktif = "Aktif" then 1 else 0 end) as total_aktif
        ')->first();

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('nama_lengkap', 'like', '%' . $request->search . '%')
                  ->orWhere('nipg', 'like', '%' . $request->search . '%');
            });
        }
        if ($request->filled('jenis')) {
            $query->where('jenis_pegawai', $request->jenis);
        }

        $pegawais = $query->latest()->paginate(15)->withQueryString();

        return view('admin.pegawai.index', compact('pegawais', 'stats'));
    }

    public function create()
    {
        $klasisList = Klasis::orderBy('nama_klasis')->get();
        return view('admin.pegawai.create', compact('klasisList'));
    }

    public function store(Request $request)
    {
        $rules = [
            'nama_lengkap' => 'required|string|max:255',
            'nipg' => 'required|string|unique:pegawai,nipg|max:30',
            'jenis_pegawai' => 'required|string',
            'status_kepegawaian' => 'required|string',
            'tanggal_lahir' => 'required|date',
            'email' => 'nullable|email|unique:users,email',
            'klasis_id' => 'nullable|exists:klasis,id',
            'jemaat_id' => 'nullable|exists:jemaat,id',
            'foto_diri' => 'nullable|image|max:2048',
        ];

        if ($request->jenis_pegawai == 'Pendeta') {
            $rules['tanggal_tahbisan'] = 'required|date';
            $rules['tempat_tahbisan'] = 'required|string|max:255';
        }

        $request->validate($rules);

        try {
            DB::transaction(function () use ($request) {
                $email = $request->email ?? strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $request->nipg) . '@gpipapua.org');
                $user = User::create([
                    'name' => $request->nama_lengkap,
                    'email' => $email,
                    'password' => Hash::make($request->nipg),
                    'klasis_id' => $request->klasis_id,
                    'jemaat_id' => $request->jemaat_id,
                ]);

                if ($request->jenis_pegawai == 'Pendeta') {
                    $user->assignRole('Pendeta');
                } else {
                    $user->assignRole('Pegawai'); 
                }

                $fotoPath = null;
                if ($request->hasFile('foto_diri')) {
                    $fotoPath = $request->file('foto_diri')->store('foto_pegawai', 'public');
                }

                $tglPensiun = \Carbon\Carbon::parse($request->tanggal_lahir)->addYears(60);

                $pegawai = Pegawai::create(array_merge($request->all(), [
                    'user_id' => $user->id,
                    'foto_diri' => $fotoPath,
                    'status_aktif' => 'Aktif',
                    'tanggal_pensiun' => $tglPensiun
                ]));
                
                $user->update(['pegawai_id' => $pegawai->id]);
            });

            return redirect()->route('admin.kepegawaian.pegawai.index')->with('success', 'Data Personil berhasil disimpan.');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    public function show(Pegawai $pegawai)
    {
        $pegawai->load(['keluarga', 'pendidikan', 'riwayatSk', 'mutasiHistory', 'klasis', 'jemaat']);
        return view('admin.pegawai.show', compact('pegawai'));
    }

    public function edit(Pegawai $pegawai)
    {
        $klasisList = Klasis::orderBy('nama_klasis')->get();
        $jemaatList = Jemaat::where('klasis_id', $pegawai->klasis_id)->get();
        return view('admin.pegawai.edit', compact('pegawai', 'klasisList', 'jemaatList'));
    }

    public function update(Request $request, Pegawai $pegawai)
    {
        $rules = [
            'nama_lengkap' => 'required|string|max:255',
            'nipg' => ['required', 'string', 'max:30', Rule::unique('pegawai')->ignore($pegawai->id)],
            'jenis_pegawai' => 'required|string',
            'status_kepegawaian' => 'required|string',
            'tanggal_lahir' => 'required|date',
            'email' => ['nullable', 'email', Rule::unique('users', 'email')->ignore($pegawai->user_id ?? 0)],
            'klasis_id' => 'nullable|exists:klasis,id',
            'jemaat_id' => 'nullable|exists:jemaat,id',
            'foto_diri' => 'nullable|image|max:2048',
        ];
        
        if ($request->jenis_pegawai == 'Pendeta') {
             $rules['tanggal_tahbisan'] = 'required|date';
             $rules['tempat_tahbisan'] = 'required|string|max:255';
        }
        
        $request->validate($rules);

        $data = $request->except(['foto_diri', 'email']);

        if ($request->hasFile('foto_diri')) {
            if ($pegawai->foto_diri && Storage::disk('public')->exists($pegawai->foto_diri)) {
                Storage::disk('public')->delete($pegawai->foto_diri);
            }
            $data['foto_diri'] = $request->file('foto_diri')->store('foto_pegawai', 'public');
        }

        // Sanitasi: Kosongkan tahbisan jika diubah menjadi non-Pendeta
        if ($request->jenis_pegawai != 'Pendeta') {
            $data['tanggal_tahbisan'] = null;
            $data['tempat_tahbisan'] = null;
        }

        // Update estimasi pensiun jika tanggal lahir berubah
        $data['tanggal_pensiun'] = \Carbon\Carbon::parse($request->tanggal_lahir)->addYears(60);

        $pegawai->update($data);
        
        if ($pegawai->user) {
            $pegawai->user->update([
                'name' => $request->nama_lengkap,
                'klasis_id' => $request->klasis_id,
                'jemaat_id' => $request->jemaat_id
            ]);
            if($request->email) {
                $pegawai->user->update(['email' => $request->email]);
            }
        }

        return redirect()->route('admin.kepegawaian.pegawai.show', $pegawai->id)->with('success', 'Data diperbarui dengan sukses.');
    }

    public function destroy(Pegawai $pegawai)
    {
        if ($pegawai->user) $pegawai->user->delete();
        $pegawai->delete();
        return redirect()->route('admin.kepegawaian.pegawai.index')->with('success', 'Data dihapus.');
    }

    public function print($id)
    {
        $pegawai = Pegawai::with(['klasis', 'jemaat', 'riwayatSk'])->findOrFail($id);
        $setting = \App\Models\Setting::firstOrCreate(['id' => 1]);
        $pdf = Pdf::loadView('admin.pegawai.pdf_biodata', compact('pegawai', 'setting'));
        $pdf->setPaper('a4', 'portrait');
        $namaFile = 'Kutipan_Buku_Induk_' . str_replace(' ', '_', $pegawai->nama_lengkap) . '.pdf';
        return $pdf->stream($namaFile);
    }    
}