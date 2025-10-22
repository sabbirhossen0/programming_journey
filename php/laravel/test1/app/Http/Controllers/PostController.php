<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    // GET all posts
    public function index()
    {
        return response()->json(Post::all());
    }

    // POST create new post
    public function store(Request $request)
    {
        $post = Post::create([
            'title' => $request->title,
            'content' => $request->input('content'),
        ]);

        return response()->json($post, 201);
    }

    // GET single post
    public function show($id)
    {
        return response()->json(Post::findOrFail($id));
    }
}
