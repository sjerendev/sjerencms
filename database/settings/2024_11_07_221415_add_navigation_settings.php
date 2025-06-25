<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddNavigationSettings extends Migration
{
	public function up(): void
	{
		$this->mergeSetting('general', [
			'navigation_type' => 'normal',
			'show_contact_in_menu' => false,
			'show_social_in_menu' => false,
			'menu_contact_info' => null,
		]);
	}

	public function down(): void
	{
		// Implement the rollback logic if necessary
	}

	protected function mergeSetting($group, $values)
	{
		$settings = DB::table('settings')->where('group', $group)->get();

		foreach ($values as $name => $value) {
			if (!$settings->where('name', $name)->count()) {
				DB::table('settings')->insert([
					'group' => $group,
					'name' => $name,
					'payload' => json_encode($value),
				]);
			}
		}
	}
}
