<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::table('pages', function (Blueprint $table) {
			$table->string('hero_headline')->nullable();
			$table->string('hero_subheadline')->nullable();
			$table->text('hero_paragraph')->nullable();
			$table->string('hero_cta_text')->nullable();
			$table->string('hero_cta_url')->nullable();
		});
	}

	public function down(): void
	{
		Schema::table('pages', function (Blueprint $table) {
			$table->dropColumn([
				'hero_headline',
				'hero_subheadline',
				'hero_paragraph',
				'hero_cta_text',
				'hero_cta_url'
			]);
		});
	}
};
