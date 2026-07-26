<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TransaksiJemaat;
use App\Models\Jemaat;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KeuanganJemaatController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $bulan = $request->bulan ?? date('m');
        $tahun = $request->tahun ?? date('Y');
        $kategori = $request->kategori_kas ?? 'Kas Induk Jemaat';
        
        $jemaatId = $user->jemaat_id; 
        
        if ($user->hasAnyRole(['Super Admin', 'Admin Sinode', 'Admin Bidang 3', 'Admin Bidang 2', 'Admin Klasis'])) {
            $jemaatId = $request->jemaat_id;
        }

        $transaksiBulanIni = collect();
        $saldoAwal = 0;
        $saldoAkhir = 0;

        if ($jemaatId) {
            $saldoAwal = TransaksiJemaat::where('jemaat_id', $jemaatId)
                ->where('kategori_kas', $kategori)
                ->where(function($q) use ($bulan, $tahun) {
                    $q->whereYear('tanggal', '<', $tahun)
                      ->orWhere(function($q2) use ($bulan, $tahun) {
                          $q2->whereYear('tanggal', $tahun)->whereMonth('tanggal', '<', $bulan);
                      });
                })
                ->selectRaw('SUM(CASE WHEN jenis_transaksi = "Pemasukan" THEN nominal ELSE -nominal END) as total')
                ->value('total') ?? 0;

            $transaksiBulanIni = TransaksiJemaat::where('jemaat_id', $jemaatId)
                ->where('kategori_kas', $kategori)
                ->whereYear('tanggal', $tahun)
                ->whereMonth('tanggal', $bulan)
                ->orderBy('tanggal', 'asc')->orderBy('id', 'asc')
                ->get();

            $saldoBerjalan = $saldoAwal;
            foreach ($transaksiBulanIni as $trx) {
                $saldoBerjalan += ($trx->jenis_transaksi == 'Pemasukan') ? $trx->nominal : -$trx->nominal;
                $trx->saldo_berjalan = $saldoBerjalan;
            }
            $saldoAkhir = $saldoBerjalan;
        }

        $jemaatList = collect();
        if ($user->hasAnyRole(['Super Admin', 'Admin Sinode', 'Admin Bidang 2'])) {
             $jemaatList = Jemaat::orderBy('nama_jemaat')->get();
        } elseif ($user->hasRole('Admin Klasis')) {
             $jemaatList = Jemaat::where('klasis_id', $user->klasis_id)->orderBy('nama_jemaat')->get();
        }

        // TANGANI REQUEST CETAK
        if ($request->has('print') && $jemaatId) {
            $jemaatAktif = Jemaat::with('klasis')->find($jemaatId);
            $setting = Setting::firstOrCreate(['id' => 1]); // AMBIL DATA SETTING LOGO
            
            // FIX: Tambahkan $setting ke dalam compact agar tidak Undefined Variable di Blade
            return view('admin.keuangan_jemaat.print', compact('transaksiBulanIni', 'saldoAwal', 'saldoAkhir', 'bulan', 'tahun', 'kategori', 'jemaatAktif', 'setting'));
        }

        return view('admin.keuangan_jemaat.index', compact('transaksiBulanIni', 'saldoAwal', 'saldoAkhir', 'bulan', 'tahun', 'kategori', 'jemaatList', 'jemaatId'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->hasRole('Admin Jemaat')) {
            abort(403, 'Hanya Admin Jemaat yang dapat mencatat kas jemaat.');
        }

        $request->validate([
            'kategori_kas' => 'required|string',
            'tanggal' => 'required|date',
            'uraian' => 'required|string',
            'kode_ayat' => 'nullable|string',
            'jenis_transaksi' => 'required|in:Pemasukan,Pengeluaran',
            'nominal' => 'required|numeric|min:1',
        ]);

        TransaksiJemaat::create([
            'jemaat_id' => Auth::user()->jemaat_id,
            'kategori_kas' => $request->kategori_kas,
            'tanggal' => $request->tanggal,
            'uraian' => $request->uraian,
            'kode_ayat' => $request->kode_ayat,
            'jenis_transaksi' => $request->jenis_transaksi,
            'nominal' => $request->nominal,
            'created_by' => Auth::id(),
        ]);

        return back()->with('success', 'Transaksi keuangan berhasil dicatat!');
    }
    
    public function destroy($id)
    {
        if (!Auth::user()->hasRole('Admin Jemaat')) {
            abort(403, 'Hanya Admin Jemaat yang dapat membatalkan transaksi.');
        }

        $trx = TransaksiJemaat::findOrFail($id);
        
        if($trx->jemaat_id != Auth::user()->jemaat_id) {
            abort(403, 'Anda tidak berhak menghapus data Jemaat lain.');
        }

        $trx->delete();
        return back()->with('success', 'Catatan transaksi telah dihapus permanen.');
    }
}