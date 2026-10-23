<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// The media table predates medialibrary 10, which writes a uuid on every file.
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('media', 'uuid')) {
            return;
        }

        Schema::table('media', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->unique()->after('model_id');
        });
    }

    public function down(): void
    {
        Schema::table('media', function (Blueprint $table) {
            $table->dropUnique(['uuid']);
            $table->dropColumn('uuid');
        });
    }
};
