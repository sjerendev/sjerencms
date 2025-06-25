<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->longText('new_content')->nullable()->after('content');
        });

        // Copy existing content if needed (you might want to add your own content migration logic here)
        
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('content');
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->renameColumn('new_content', 'content');
        });
    }

    public function down()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->json('new_content')->nullable()->after('content');
            $table->dropColumn('content');
            $table->renameColumn('new_content', 'content');
        });
    }
};
