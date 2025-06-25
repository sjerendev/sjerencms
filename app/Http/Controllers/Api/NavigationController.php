<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Page;

class NavigationController extends Controller
{
	public function index()
	{
		return Page::where('is_published', true)
			->where('show_in_main_nav', true)
			->orderBy('sort_order')
			->orderBy('created_at', 'asc')
			->select('id', 'title', 'slug', 'parent_id')
			->get();
	}

	public function footer()
	{
		return Page::where('is_published', true)
			->where('show_in_footer_nav', true)
			->orderBy('sort_order')
			->orderBy('created_at', 'asc')
			->select('id', 'title', 'slug')
			->get();
	}
}
