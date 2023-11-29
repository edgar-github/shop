<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Post;

class PostsController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLastPosts(): \Illuminate\Http\JsonResponse
    {
        $getPosts = Post::orderBy('id', 'desc')
            ->limit(Post::API_LAST_POSTS_LIMIT)
            ->get();

        $data = PostResource::collection($getPosts);

        return response()->json($data);
    }
}
