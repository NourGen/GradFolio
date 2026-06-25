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
            $table->foreignId('portfolio_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description');
            $table->string('tech_stack')->nullable(); // comma-separated technologies
            $table->string('project_url', 2048)->nullable();
            $table->string('github_url', 2048)->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index('portfolio_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
