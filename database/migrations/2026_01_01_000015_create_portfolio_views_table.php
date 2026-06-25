<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portfolio_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portfolio_id')->constrained()->cascadeOnDelete();
            $table->string('ip_hash', 64);  // SHA-256 hashed IP (privacy-safe)
            $table->string('user_agent', 512)->nullable();
            $table->timestamp('viewed_at')->useCurrent();

            // Composite index for range queries (views per day/week/month)
            $table->index(['portfolio_id', 'viewed_at'], 'views_perf_index');
            // Composite index for deduplication within 24h window
            $table->index(['portfolio_id', 'ip_hash'], 'views_unique_daily_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolio_views');
    }
};
