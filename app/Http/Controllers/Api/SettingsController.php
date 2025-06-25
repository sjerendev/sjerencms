<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Settings\GeneralSettings;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
	public function index(GeneralSettings $settings)
	{
		return response()->json([
			'navigation_type' => $settings->navigation_type,
			'show_contact_in_menu' => $settings->show_contact_in_menu,
			'show_social_in_menu' => $settings->show_social_in_menu,
			'menu_contact_info' => $settings->menu_contact_info,
			'facebook_url' => $settings->facebook_url,
			'instagram_url' => $settings->instagram_url,
			'linkedin_url' => $settings->linkedin_url,
			'logo' => $settings->logo ? Storage::url($settings->logo) : null,
			'site_name' => $settings->site_name,
		]);
	}
}
