<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\SakramenBaptis;
use App\Models\SakramenSidi;
use App\Models\SakramenNikah;
use App\Models\Setting;
use Carbon\Carbon;

class SakramenCetakController extends Controller
{
    /**
     * Cetak Surat Baptis
     */
    public function cetakBaptis($id)
    {
        // Set Locale ke Indonesia
        Carbon::setLocale('id');

        $data = SakramenBaptis::with(['anggotaJemaat.jemaat.klasis'])->findOrFail($id);
        $setting = Setting::first(); 
        
        $pdf = Pdf::loadView('admin.sakramen.cetak.baptis', compact('data', 'setting'));
        $pdf->setPaper('a4', 'portrait');
        
        return $pdf->stream('Surat-Baptis-' . $data->anggotaJemaat->nama_lengkap . '.pdf');
    }

    /**
     * Cetak Surat Sidi
     */
    public function cetakSidi($id)
    {
        Carbon::setLocale('id');

        $data = SakramenSidi::with(['anggotaJemaat.jemaat.klasis'])->findOrFail($id);
        $setting = Setting::first();
        
        $pdf = Pdf::loadView('admin.sakramen.cetak.sidi', compact('data', 'setting'));
        $pdf->setPaper('a4', 'portrait');
        
        return $pdf->stream('Surat-Sidi-' . $data->anggotaJemaat->nama_lengkap . '.pdf');
    }

    /**
     * Cetak Akta Nikah (F4 Potrait - 1 Lembar)
     */
    public function cetakNikah($id)
    {
        Carbon::setLocale('id');
        
        $data = SakramenNikah::with(['suami.jemaat', 'istri.jemaat'])->findOrFail($id);
        $setting = Setting::first();

        $pdf = Pdf::loadView('admin.sakramen.cetak.nikah', compact('data', 'setting'));
        
        // Setting Ukuran Kertas F4 (Folio) - 21.5 cm x 33 cm
        // Konversi cm ke point: 1 cm = 28.346 pt
        $width = 21.5 * 28.346; 
        $height = 33 * 28.346;
        $customPaper = [0, 0, $width, $height];
        
        $pdf->setPaper($customPaper, 'portrait');
        
        return $pdf->stream('Akta-Nikah-' . $data->no_akta_nikah . '.pdf');
    }
}