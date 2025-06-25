<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
	public string $site_name;
	public string $tagline;
	public ?string $email = null;
	public ?string $phone = null;
	public ?string $address = null;
	public ?string $facebook_url;
	public ?string $instagram_url;
	public ?string $linkedin_url;
	public ?string $logo;
	public string $navigation_type = 'normal';
	public bool $show_contact_in_menu = false;
	public bool $show_social_in_menu = false;
	public ?string $menu_contact_info = null;
	public ?string $head_code_snippets = null;
	public ?string $footer_code_snippets = null;

	public static function group(): string
	{
		return 'general';
	}
}
