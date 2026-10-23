<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tracks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('release_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('position');
            $table->string('title');
            $table->unsignedInteger('duration_seconds');
            $table->unsignedInteger('min_price')->default(20);
            $table->timestamps();

            $table->unique(['release_id', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tracks');
    }
};
