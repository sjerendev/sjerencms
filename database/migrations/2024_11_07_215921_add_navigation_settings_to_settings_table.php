<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::table('settings', function (Blueprint $table) {
			$table->string('navigation_type')->default('normal');
			$table->boolean('show_contact_in_menu')->default(false);
			$table->boolean('show_social_in_menu')->default(false);
			$table->text('menu_contact_info')->nullable();
		});
	}

	public function down(): void
	{
		Schema::table('settings', function (Blueprint $table) {
			$table->dropColumn([
				'navigation_type',
				'show_contact_in_menu',
				'show_social_in_menu',
				'menu_contact_info'
			]);
		});
	}
};
