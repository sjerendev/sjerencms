<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;

class NavigationController extends Controller
{
	public function index(Request $request)
	{
		$language = $this->getLanguageFromRequest($request);
		
		return Page::where('is_published', true)
			->where('show_in_main_nav', true)
			->where('language', $language)
			->orderBy('sort_order')
			->orderBy('created_at', 'asc')
			->select('id', 'title', 'slug', 'parent_id')
			->get();
	}

	public function footer(Request $request)
	{
		$language = $this->getLanguageFromRequest($request);
		
		return Page::where('is_published', true)
			->where('show_in_footer_nav', true)
			->where('language', $language)
			->orderBy('sort_order')
			->orderBy('created_at', 'asc')
			->select('id', 'title', 'slug')
			->get();
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
