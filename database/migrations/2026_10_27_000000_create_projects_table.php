<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('client');
            $table->string('headline_sv');
            $table->string('headline_en');
            $table->string('role_sv')->nullable();
            $table->string('role_en')->nullable();
            $table->json('services');
            $table->string('tech')->nullable();
            $table->string('url');
            $table->boolean('rebuilt')->default(false);
            $table->unsignedInteger('position');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
