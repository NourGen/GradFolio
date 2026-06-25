<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portfolio_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->enum('level', ['beginner', 'intermediate', 'advanced', 'expert'])->default('intermediate');
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index('portfolio_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('skills');
    }
};
