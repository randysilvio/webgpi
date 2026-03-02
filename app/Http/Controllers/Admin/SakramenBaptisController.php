<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\SakramenBaptis;
use App\Models\AnggotaJemaat;
use App\Models\Setting; 

class SakramenBaptisController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = SakramenBaptis::with(['anggotaJemaat.jemaat.klasis']);

        if ($user->hasRole('Admin Klasis') && $user->klasis_id) {
            $query->whereHas('anggotaJemaat.jemaat', fn($q) => $q->where('klasis_id', $user->klasis_id));
        } elseif ($user->hasRole('Admin Jemaat') && $user->jemaat_id) {
            $query->whereHas('anggotaJemaat', fn($q) => $q->where('jemaat_id', $user->jemaat_id));
        }

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->whereHas('anggotaJemaat', fn($sub) => $sub->where('nama_lengkap', 'like', "%{$request->search}%"))
                  ->orWhere('no_akta_baptis', 'like', "%{$request->search}%");
            });
        }

        $baptis = $query->latest('tanggal_baptis')->paginate(15);

        return view('admin.sakramen.baptis.index', compact('baptis'));
    }

    public function create()
    {
        $user = Auth::user();
        $anggotaQuery = AnggotaJemaat::where('status_keanggotaan', 'Aktif')
            ->whereNull('tanggal_baptis')
            ->doesntHave('dataBaptis'); 

        if ($user->hasRole('Admin Klasis') && $user->klasis_id) {
            $anggotaQuery->whereHas('jemaat', fn($q) => $q->where('klasis_id', $user->klasis_id));
        } elseif ($user->hasRole('Admin Jemaat') && $user->jemaat_id) {
            $anggotaQuery->where('jemaat_id', $user->jemaat_id);
        }

        $anggotaTanpaBaptis = $anggotaQuery->with('jemaat:id,nama_jemaat')
            ->orderBy('nama_lengkap')
            ->get(['id', 'nama_lengkap', 'jemaat_id', 'tanggal_lahir']); 

        return view('admin.sakramen.baptis.create', compact('anggotaTanpaBaptis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'anggota_jemaat_id' => 'required|exists:anggota_jemaat,id',
            'no_akta_baptis'    => 'required|unique:sakramen_baptis,no_akta_baptis',
            'tanggal_baptis'    => 'required|date',
            'tempat_baptis'     => 'required|string',
            'pendeta_pelayan'   => 'required|string'
        ]);

        try {
            DB::beginTransaction();

            $baptis = SakramenBaptis::create([
                'anggota_jemaat_id' => $request->anggota_jemaat_id,
                'no_akta_baptis'    => $request->no_akta_baptis,
                'tanggal_baptis'    => $request->tanggal_baptis,
                'tempat_baptis'     => $request->tempat_baptis,
                'pendeta_pelayan'   => $request->pendeta_pelayan,
            ]);

            $anggota = AnggotaJemaat::findOrFail($request->anggota_jemaat_id);
            $anggota->update([
                'tanggal_baptis' => $request->tanggal_baptis,
                'tempat_baptis'  => $request->tempat_baptis
            ]);

            DB::commit();
            return redirect()->route('admin.sakramen.baptis.index')->with('success', 'Data Baptisan berhasil dicatat.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyimpan: ' . $e->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        $baptis = SakramenBaptis::findOrFail($id);
        return view('admin.sakramen.baptis.edit', compact('baptis'));
    }

    public function update(Request $request, $id)
    {
        $baptis = SakramenBaptis::findOrFail($id);

        $request->validate([
            'no_akta_baptis'    => 'required|unique:sakramen_baptis,no_akta_baptis,'.$id,
            'tanggal_baptis'    => 'required|date',
            'tempat_baptis'     => 'required|string',
            'pendeta_pelayan'   => 'required|string'
        ]);

        try {
            DB::beginTransaction();
            $baptis->update($request->all());

            $anggota = AnggotaJemaat::find($baptis->anggota_jemaat_id);
            if ($anggota) {
                $anggota->update([
                    'tanggal_baptis' => $request->tanggal_baptis,
                    'tempat_baptis'  => $request->tempat_baptis
                ]);
            }

            DB::commit();
            return redirect()->route('admin.sakramen.baptis.index')->with('success', 'Data baptisan berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $baptis = SakramenBaptis::findOrFail($id);
        $anggota = AnggotaJemaat::find($baptis->anggota_jemaat_id);

        if ($anggota) {
            $anggota->update([
                'tanggal_baptis' => null,
                'tempat_baptis'  => null
            ]);
        }

        $baptis->delete();
        return redirect()->route('admin.sakramen.baptis.index')->with('success', 'Data baptisan berhasil dihapus.');
    }

    /**
     * Cetak Surat Baptis
     */
    public function cetakSurat($id)
    {
        $data = SakramenBaptis::with(['anggotaJemaat.jemaat.klasis', 'anggotaJemaat.ayah', 'anggotaJemaat.ibu'])->findOrFail($id);
        $setting = Setting::first(); 

        // PERBAIKAN: Mengarah ke folder 'cetak'
        return view('admin.sakramen.cetak.baptis', compact('data', 'setting'));
    }
}