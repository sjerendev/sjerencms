<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	/**
	 * Run the migrations.
	 */
	public function up()
	{
		Schema::table('posts', function (Blueprint $table) {
			$table->string('hero_background_type')->nullable();
			$table->string('hero_background_color')->nullable();
			$table->string('hero_text_color')->nullable();
		});
	}

	public function down()
	{
		Schema::table('posts', function (Blueprint $table) {
			$table->dropColumn([
				'hero_background_type',
				'hero_background_color',
				'hero_text_color'
			]);
		});
	}
};
