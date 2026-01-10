<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $language = $this->getLanguageFromRequest($request);
        
        $query = Post::where('is_published', true)
            ->where('language', $language)
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

    public function show($slug, Request $request)
    {
        $language = $this->getLanguageFromRequest($request);
        
        $post = Post::where('slug', $slug)
            ->where('language', $language)
            ->where('is_published', true)
            ->with(['categories', 'author'])
            ->firstOrFail();

        return response()->json($post);
    }
    
    private function getLanguageFromRequest(Request $request)
     {
         // Check if language is provided as route parameter
         if ($request->route('lang')) {
             return $request->route('lang');
         }
         
         // Check if language is provided as query parameter
         if ($request->has('lang')) {
             return $request->get('lang');
         }
         
         // Check referer URL to determine language
         $referer = $request->header('referer');
         if ($referer && str_contains($referer, '/en/')) {
             return 'en';
         }
         
         // Default to Swedish
         return 'sv';
     }
}
