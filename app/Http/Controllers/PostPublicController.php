<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Setting; // Import Setting model

class PostPublicController extends Controller
{
    /**
     * Menampilkan daftar semua berita/kegiatan yang sudah dipublish (arsip).
     */
    public function index()
    {
        $setting = Setting::firstOrCreate(['id' => 1]); // Ambil data setting
        $posts = Post::whereNotNull('published_at')
                     ->where('published_at', '<=', now())
                     ->latest('published_at')
                     ->paginate(9); // Paginasi misal 9 post per halaman

        // Kirim data setting dan posts ke view arsip
        return view('posts.index-public', compact('setting', 'posts'));
    }

    /**
     * Menampilkan detail berita/kegiatan tunggal.
     */
    public function show($slug) // Terima slug dari route
    {
        $setting = Setting::firstOrCreate(['id' => 1]); // Ambil data setting
        $post = Post::where('slug', $slug)
                    ->whereNotNull('published_at')
                    ->where('published_at', '<=', now())
                    ->firstOrFail(); // Cari berdasarkan slug, pastikan published, 404 jika tidak ketemu

        // Kirim data setting dan post ke view detail
        return view('posts.show-public', compact('setting', 'post'));
    }
}