<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Setting; 

class PostPublicController extends Controller
{
    public function index()
    {
        $setting = Setting::firstOrCreate(['id' => 1]); 
        $posts = Post::where('status', 'published') // PENGAMANAN BARU
                     ->whereNotNull('published_at')
                     ->where('published_at', '<=', now())
                     ->latest('published_at')
                     ->paginate(9); 

        return view('posts.index-public', compact('setting', 'posts'));
    }

    public function show($slug) 
    {
        $setting = Setting::firstOrCreate(['id' => 1]); 
        $post = Post::where('slug', $slug)
                    ->where('status', 'published') // PENGAMANAN BARU
                    ->whereNotNull('published_at')
                    ->where('published_at', '<=', now())
                    ->firstOrFail(); 

        return view('posts.show-public', compact('setting', 'post'));
    }
}