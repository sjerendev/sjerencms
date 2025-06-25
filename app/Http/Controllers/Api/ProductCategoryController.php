<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductCategory;

class ProductCategoryController extends Controller
{
    public function index()
    {
        $categories = ProductCategory::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return response()->json($categories);
    }
}
