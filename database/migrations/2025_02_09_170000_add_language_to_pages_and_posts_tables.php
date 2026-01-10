<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->string('language', 2)->default('sv')->after('slug');
            $table->dropUnique(['slug']);
            $table->unique(['slug', 'language']);
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->string('language', 2)->default('sv')->after('slug');
            $table->dropUnique(['slug']);
            $table->unique(['slug', 'language']);
        });
    }

    public function down()
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropUnique(['slug', 'language']);
            $table->dropColumn('language');
            $table->unique('slug');
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->dropUnique(['slug', 'language']);
            $table->dropColumn('language');
            $table->unique('slug');
        });
    }
};