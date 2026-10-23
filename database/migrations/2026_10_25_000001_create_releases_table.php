<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('releases', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('artist');
            $table->string('title');
            $table->date('released_on');
            $table->text('description')->nullable();
            $table->unsignedInteger('min_price');
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('releases');
    }
};
