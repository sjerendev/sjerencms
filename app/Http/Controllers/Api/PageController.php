<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Page;

class PageController extends Controller
{
	public function index()
	{
		return Page::where('is_published', true)
			->orderBy('sort_order')
			->select(['id', 'title', 'slug', 'parent_id'])
			->get();
	}

	public function show($slug)
	{
		$page = Page::where('slug', $slug)
			->where('is_published', true)
			->firstOrFail();

		return response()->json($page);
	}

	public function home()
	{
		return Page::where('is_homepage', true)
			->where('is_published', true)
			->firstOrFail();
	}
}
