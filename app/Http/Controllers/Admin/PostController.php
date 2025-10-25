<?php
// app/Http/Controllers/Admin/PostController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post; // Import model Post
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // Untuk file handling
use Illuminate\Support\Str; // Untuk membuat slug
use Illuminate\Support\Facades\Log; // Untuk logging error

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Ambil semua post, urutkan dari terbaru, paginasi 10 per halaman
        $posts = Post::latest()->paginate(10);
        return view('admin.posts.index', compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.posts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255|unique:posts,title', // Judul unik
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Validasi gambar
            'publish_now' => 'nullable|boolean', // Checkbox untuk publish
            'published_at_date' => 'nullable|date_format:Y-m-d', // Tanggal publish
            'published_at_time' => 'nullable|date_format:H:i', // Waktu publish
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('posts', 'public');
        }

        $publishedAt = null;
        if ($request->filled('publish_now')) {
            $publishedAt = now();
        } elseif ($request->filled('published_at_date') && $request->filled('published_at_time')) {
             try {
                 $publishedAt = \Carbon\Carbon::createFromFormat('Y-m-d H:i', $validatedData['published_at_date'] . ' ' . $validatedData['published_at_time']);
             } catch (\Exception $e) {
                 // Tangani jika format tanggal/waktu salah, mungkin set default ke null atau beri error
                 Log::error("Invalid date/time format for publish: " . $e->getMessage());
                 return back()->withInput()->with('error', 'Format tanggal atau waktu publikasi tidak valid.');
             }
        }


        try {
            Post::create([
                'title' => $validatedData['title'],
                'slug' => Str::slug($validatedData['title']), // Buat slug otomatis
                'content' => $validatedData['content'],
                'image_path' => $imagePath,
                'published_at' => $publishedAt,
            ]);
        } catch (\Exception $e) {
             Log::error('Error creating post: ' . $e->getMessage());
             // Hapus gambar jika post gagal dibuat
             if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
             }
             return back()->withInput()->with('error', 'Gagal membuat postingan. Silakan coba lagi.');
        }


        return redirect()->route('admin.posts.index')->with('success', 'Berita/Kegiatan berhasil dibuat!');
    }

    /**
     * Display the specified resource.
     * (Tidak kita gunakan di admin, biasanya untuk API atau detail publik)
     */
    public function show(Post $post)
    {
        // Biasanya tidak digunakan untuk admin index/edit
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post) // Otomatis inject model berdasarkan ID di URL
    {
         return view('admin.posts.edit', compact('post'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
         $validatedData = $request->validate([
            'title' => 'required|string|max:255|unique:posts,title,' . $post->id, // Judul unik kecuali untuk dirinya sendiri
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'publish_now' => 'nullable|boolean',
            'published_at_date' => 'nullable|date_format:Y-m-d',
            'published_at_time' => 'nullable|date_format:H:i',
            'remove_image' => 'nullable|boolean', // Checkbox hapus gambar
        ]);

        $updateData = [
            'title' => $validatedData['title'],
            'slug' => Str::slug($validatedData['title']),
            'content' => $validatedData['content'],
        ];

        // Proses Update Gambar
        if ($request->hasFile('image')) {
            // Hapus gambar lama
            if ($post->image_path && Storage::disk('public')->exists($post->image_path)) {
                Storage::disk('public')->delete($post->image_path);
            }
            // Simpan gambar baru
            $updateData['image_path'] = $request->file('image')->store('posts', 'public');
        } elseif ($request->filled('remove_image')) {
             // Hapus gambar lama jika dicentang dan tidak ada gambar baru
            if ($post->image_path && Storage::disk('public')->exists($post->image_path)) {
                Storage::disk('public')->delete($post->image_path);
            }
            $updateData['image_path'] = null; // Set path jadi null
        }
        // Jika tidak ada file baru DAN tidak dicentang remove, image_path tidak diubah

        // Proses Update Tanggal Publikasi
        if ($request->filled('publish_now')) {
            $updateData['published_at'] = now();
        } elseif ($request->filled('published_at_date') && $request->filled('published_at_time')) {
             try {
                 $updateData['published_at'] = \Carbon\Carbon::createFromFormat('Y-m-d H:i', $validatedData['published_at_date'] . ' ' . $validatedData['published_at_time']);
             } catch (\Exception $e) {
                  Log::error("Invalid date/time format for publish update: " . $e->getMessage());
                 return back()->withInput()->with('error', 'Format tanggal atau waktu publikasi tidak valid.');
             }
        } else {
             // Jika tidak dicentang publish now dan tanggal/waktu tidak diisi, set jadi draft (null)
             $updateData['published_at'] = null;
        }

        try {
            $post->update($updateData);
        } catch (\Exception $e) {
             Log::error('Error updating post: ' . $e->getMessage());
             return back()->withInput()->with('error', 'Gagal memperbarui postingan. Silakan coba lagi.');
        }

        return redirect()->route('admin.posts.index')->with('success', 'Berita/Kegiatan berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        try {
             // Hapus gambar terkait jika ada
             if ($post->image_path && Storage::disk('public')->exists($post->image_path)) {
                Storage::disk('public')->delete($post->image_path);
             }
             // Hapus record dari database
             $post->delete();
        } catch (\Exception $e) {
              Log::error('Error deleting post: ' . $e->getMessage());
             return back()->with('error', 'Gagal menghapus postingan. Silakan coba lagi.');
        }


        return redirect()->route('admin.posts.index')->with('success', 'Berita/Kegiatan berhasil dihapus!');
    }
}