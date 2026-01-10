<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
	public function index(Request $request)
	{
		$language = $this->getLanguageFromRequest($request);
		
		return Page::where('is_published', true)
			->where('language', $language)
			->orderBy('sort_order')
			->select(['id', 'title', 'slug', 'parent_id', 'language'])
			->get();
	}

	public function show($slug, Request $request)
	{
		$language = $this->getLanguageFromRequest($request);
		
		$page = Page::where('slug', $slug)
			->where('language', $language)
			->where('is_published', true)
			->firstOrFail();

		return response()->json($page);
	}

	public function home(Request $request)
	{
		$language = $this->getLanguageFromRequest($request);
		
		return Page::where('is_homepage', true)
			->where('language', $language)
			->where('is_published', true)
			->firstOrFail();
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
