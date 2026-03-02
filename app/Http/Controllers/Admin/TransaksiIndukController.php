<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TransaksiInduk;
use App\Models\MataAnggaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TransaksiIndukController extends Controller
{
    /**
     * Menampilkan Buku Kas Umum (BKU).
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = TransaksiInduk::with(['mataAnggaran', 'creator']);

        // --- Filter Wilayah (Scoping) ---
        if ($user->hasRole('Admin Klasis')) {
            $query->where('klasis_id', $user->klasis_id);
        } elseif ($user->hasRole('Admin Jemaat')) {
            $query->where('jemaat_id', $user->jemaat_id);
        }

        // --- Filter Pencarian & Tanggal ---
        if ($request->filled('search')) {
            $query->where('keterangan', 'like', '%' . $request->search . '%')
                  ->orWhere('nomor_bukti', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal_transaksi', $request->bulan);
        }

        $transaksis = $query->orderBy('tanggal_transaksi', 'desc')->paginate(20)->withQueryString();

        return view('admin.perbendaharaan.transaksi.index', compact('transaksis'));
    }

    /**
     * Form input transaksi baru.
     */
    public function create()
    {
        $mataAnggarans = MataAnggaran::where('is_active', true)->orderBy('kode')->get();
        return view('admin.perbendaharaan.transaksi.create', compact('mataAnggarans'));
    }

    /**
     * Menyimpan transaksi ke BKU.
     */
    public function store(Request $request)
    {
        $request->validate([
            'mata_anggaran_id' => 'required|exists:mata_anggaran,id',
            'tanggal_transaksi' => 'required|date',
            'nominal' => 'required|numeric|min:1',
            'keterangan' => 'required|string',
            'file_bukti' => 'nullable|image|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $user = Auth::user();
        $data = $request->all();
        $data['klasis_id'] = $user->klasis_id;
        $data['jemaat_id'] = $user->jemaat_id;
        $data['created_by'] = $user->id;

        // Handle Upload Bukti Transaksi
        if ($request->hasFile('file_bukti')) {
            $data['file_bukti_path'] = $request->file('file_bukti')->store('bukti_transaksi', 'public');
        }

        TransaksiInduk::create($data);

        return redirect()->route('admin.perbendaharaan.transaksi.index')
                         ->with('success', 'Transaksi berhasil dicatat dan anggaran diperbarui otomatis.');
    }

    /**
     * Menghapus transaksi.
     */
    public function destroy(TransaksiInduk $transaksi)
    {
        if ($transaksi->file_bukti_path) {
            Storage::disk('public')->delete($transaksi->file_bukti_path);
        }
        
        $transaksi->delete();

        return redirect()->route('admin.perbendaharaan.transaksi.index')
                         ->with('success', 'Transaksi telah dibatalkan.');
    }
}