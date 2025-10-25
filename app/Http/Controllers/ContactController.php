<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message; // Import model
use Illuminate\Support\Facades\Log; // Import Log
use Illuminate\Support\Facades\Validator; // Import Validator

class ContactController extends Controller
{
    /**
     * Simpan pesan baru dari formulir kontak publik.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return back()
                        ->withErrors($validator)
                        ->withInput()
                        ->with('error', 'Gagal mengirim pesan. Periksa kembali isian Anda.');
        }

        // Coba simpan ke database
        try {
            Message::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'subject' => $request->subject,
                'message' => $request->message,
                'is_read' => false,
            ]);

            // Redirect kembali ke halaman utama (bagian kontak) dengan pesan sukses
            return redirect(route('home') . '#kontak')->with('success', 'Pesan Anda berhasil terkirim! Terima kasih.');

        } catch (\Exception $e) {
            // Catat error
            Log::error('Error saving contact message: ' . $e->getMessage());

            // Redirect kembali dengan pesan error
            return back()
                        ->withInput()
                        ->with('error', 'Terjadi kesalahan pada server. Silakan coba lagi nanti.');
        }
    }
}