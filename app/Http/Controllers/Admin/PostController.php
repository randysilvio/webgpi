<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Post::with('author')->latest();

        // LOGIKA PEMISAHAN DATA BERDASARKAN ROLE
        if (!$user->hasAnyRole(['Super Admin', 'Admin Bidang 4'])) {
            // Jika bukan pusat, hanya bisa lihat beritanya sendiri
            $query->where('author_id', $user->id);
        } else {
            // Jika Admin Bidang 4 / Pusat, beri opsi filter tab 'pending'
            if ($request->has('status') && $request->status == 'pending') {
                $query->where('status', 'pending');
            }
        }

        $posts = $query->paginate(10);
        
        // Hitung badge notifikasi untuk Admin Bidang 4
        $pendingCount = Post::where('status', 'pending')->count();

        return view('admin.posts.index', compact('posts', 'pendingCount'));
    }

    public function create()
    {
        return view('admin.posts.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255|unique:posts,title',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'publish_now' => 'nullable|boolean',
            'published_at_date' => 'nullable|date_format:Y-m-d',
            'published_at_time' => 'nullable|date_format:H:i',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('posts', 'public');
        }

        $user = auth()->user();
        $canPublish = $user->hasAnyRole(['Super Admin', 'Admin Bidang 4']);
        $status = 'draft';
        $publishedAt = null;

        // LOGIKA PERSETUJUAN BERJENJANG
        if ($request->filled('publish_now') || ($request->filled('published_at_date') && $request->filled('published_at_time'))) {
            if ($canPublish) {
                $status = 'published';
                if ($request->filled('publish_now')) {
                    $publishedAt = now();
                } else {
                    $publishedAt = \Carbon\Carbon::createFromFormat('Y-m-d H:i', $validatedData['published_at_date'] . ' ' . $validatedData['published_at_time']);
                }
            } else {
                $status = 'pending'; // Otomatis tertahan minta persetujuan
            }
        }

        try {
            Post::create([
                'title' => $validatedData['title'],
                'slug' => Str::slug($validatedData['title']),
                'content' => $validatedData['content'],
                'image_path' => $imagePath,
                'published_at' => $publishedAt,
                'status' => $status,
                'author_id' => $user->id,
            ]);
        } catch (\Exception $e) {
             Log::error('Error creating post: ' . $e->getMessage());
             if ($imagePath && Storage::disk('public')->exists($imagePath)) Storage::disk('public')->delete($imagePath);
             return back()->withInput()->with('error', 'Gagal membuat postingan.');
        }

        $message = $canPublish ? 'Berita berhasil dipublish!' : 'Draf berita berhasil dikirim. Menunggu persetujuan Admin Bidang 4.';
        return redirect()->route('admin.posts.index')->with('success', $message);
    }

    public function show(Post $post) { abort(404); }

    public function edit(Post $post)
    {
        return view('admin.posts.edit', compact('post'));
    }

    public function update(Request $request, Post $post)
    {
         $validatedData = $request->validate([
            'title' => 'required|string|max:255|unique:posts,title,' . $post->id,
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'action_type' => 'nullable|string|in:publish,draft,pending', // Untuk tombol khusus
            'remove_image' => 'nullable|boolean',
        ]);

        $updateData = [
            'title' => $validatedData['title'],
            'slug' => Str::slug($validatedData['title']),
            'content' => $validatedData['content'],
        ];

        if ($request->hasFile('image')) {
            if ($post->image_path && Storage::disk('public')->exists($post->image_path)) Storage::disk('public')->delete($post->image_path);
            $updateData['image_path'] = $request->file('image')->store('posts', 'public');
        } elseif ($request->filled('remove_image')) {
            if ($post->image_path && Storage::disk('public')->exists($post->image_path)) Storage::disk('public')->delete($post->image_path);
            $updateData['image_path'] = null;
        }

        $user = auth()->user();
        
        // Logika Approval untuk Admin Bidang 4 saat Update
        if ($request->filled('action_type') && $user->hasAnyRole(['Super Admin', 'Admin Bidang 4'])) {
            if ($request->action_type == 'publish') {
                $updateData['status'] = 'published';
                $updateData['published_at'] = now();
            } elseif ($request->action_type == 'draft') {
                $updateData['status'] = 'draft';
                $updateData['published_at'] = null;
            }
        }

        try {
            $post->update($updateData);
        } catch (\Exception $e) {
             Log::error('Error updating post: ' . $e->getMessage());
             return back()->withInput()->with('error', 'Gagal memperbarui postingan.');
        }

        return redirect()->route('admin.posts.index')->with('success', 'Berita berhasil diperbarui!');
    }

    public function destroy(Post $post)
    {
        try {
             if ($post->image_path && Storage::disk('public')->exists($post->image_path)) {
                Storage::disk('public')->delete($post->image_path);
             }
             $post->delete();
        } catch (\Exception $e) {
              Log::error('Error deleting post: ' . $e->getMessage());
             return back()->with('error', 'Gagal menghapus postingan.');
        }
        return redirect()->route('admin.posts.index')->with('success', 'Berita berhasil dihapus!');
    }
}