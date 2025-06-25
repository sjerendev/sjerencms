<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product; // Added the Product model
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['category', 'addons' => function($query) {
            $query->where('is_active', true)->orderBy('sort_order');
        }])
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return response()->json($products);
    }

    public function show($slug)
    {
        $product = Product::with(['category', 'addons' => function($query) {
            $query->where('is_active', true)->orderBy('sort_order');
        }])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        return response()->json($product);
    }
}
