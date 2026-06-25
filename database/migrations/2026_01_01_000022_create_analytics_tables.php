<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // CV Downloads tracking table
        Schema::create('cv_downloads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portfolio_id')->constrained()->cascadeOnDelete();
            $table->string('ip_hash', 64);
            $table->string('user_agent', 512)->nullable();
            $table->timestamp('downloaded_at')->useCurrent();

            $table->index(['portfolio_id', 'downloaded_at'], 'cv_downloads_perf_index');
            $table->index(['portfolio_id', 'ip_hash'], 'cv_downloads_unique_daily_index');
        });

        // Project Clicks tracking table
        Schema::create('project_clicks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('ip_hash', 64);
            $table->string('user_agent', 512)->nullable();
            $table->timestamp('clicked_at')->useCurrent();

            $table->index(['project_id', 'clicked_at'], 'project_clicks_perf_index');
            $table->index(['project_id', 'ip_hash'], 'project_clicks_unique_daily_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_clicks');
        Schema::dropIfExists('cv_downloads');
    }
};
