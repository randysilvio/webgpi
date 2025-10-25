<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MessageController extends Controller
{
    /**
     * Menampilkan daftar semua pesan.
     */
    public function index()
    {
        $messages = Message::latest()->paginate(15); // Ambil pesan terbaru, 15 per halaman
        return view('admin.messages', compact('messages'));
    }

    /**
     * Menampilkan satu pesan (dan tandai sudah dibaca).
     */
    public function show(Message $message) // Gunakan Route Model Binding
    {
        // Tandai sebagai sudah dibaca
        if (!$message->is_read) {
            $message->update(['is_read' => true]);
        }
        
        return view('admin.messages-show', compact('message'));
    }

    /**
     * Menghapus pesan.
     */
    public function destroy(Message $message) // Gunakan Route Model Binding
    {
        try {
            $message->delete();
            return redirect()->route('admin.messages')->with('success', 'Pesan berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Error deleting message: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus pesan.');
        }
    }
}