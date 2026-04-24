<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;

class StudentPostController extends Controller
{
    /*
    |----------------------------
    | LIST POSTS (FEED)
    |----------------------------
    */
    public function index(Request $request)
    {
        $student = $request->student;

        $posts = Post::with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'data' => $posts->map(function ($post) {
                return [
                    'id' => $post->id,
                    'title' => $post->title,
                    'content' => $post->content,
                    'type' => $post->type,
                    'author' => $post->user->name ?? 'System',
                    'created_at' => $post->created_at,
                ];
            })
        ]);
    }

    /*
    |----------------------------
    | SINGLE POST
    |----------------------------
    */
    public function show($id)
    {
        $post = Post::with('user')->findOrFail($id);

        return response()->json([
            'id' => $post->id,
            'title' => $post->title,
            'content' => $post->content,
            'type' => $post->type,
            'author' => $post->user->name ?? 'System',
            'created_at' => $post->created_at,
        ]);
    }
}