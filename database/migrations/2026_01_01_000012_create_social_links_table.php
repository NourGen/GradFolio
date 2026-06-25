<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('social_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portfolio_id')->constrained()->cascadeOnDelete();
            $table->string('platform', 50); // linkedin, github, twitter, website, etc.
            $table->string('url', 2048);
            $table->timestamps();

            $table->index('portfolio_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('social_links');
    }
};
