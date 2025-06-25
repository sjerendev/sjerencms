<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::where('is_published', true)
            ->with(['categories', 'author'])
            ->orderBy('publish_date', 'desc')
            ->orderBy('created_at', 'desc');

        // Filter by category if provided
        if ($request->has('categories')) {
            $categoryId = $request->categories;
            $query->whereHas('categories', function($q) use ($categoryId) {
                $q->where('categories.id', $categoryId);
            });
        }

        // Use custom limit if provided, otherwise default to 9
        $limit = $request->input('limit', 9);
        
        return $query->paginate($limit);
    }

    public function show($slug)
    {
        $post = Post::where('slug', $slug)
            ->where('is_published', true)
            ->with(['categories', 'author'])
            ->firstOrFail();

        return response()->json($post);
    }
}
