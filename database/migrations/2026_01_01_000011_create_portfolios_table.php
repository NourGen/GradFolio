<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portfolios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->unique();
            $table->string('title')->nullable();
            $table->string('headline')->nullable(); // Short tagline e.g. "Full-Stack Developer"
            $table->text('bio')->nullable();
            $table->string('location')->nullable();
            $table->string('profile_picture_path')->nullable();
            $table->string('cv_path')->nullable();
            $table->boolean('is_published')->default(false);
            $table->string('slug')->unique()->nullable(); // e.g. /portfolio/john-doe
            $table->timestamps();

            $table->index('is_published');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolios');
    }
};
