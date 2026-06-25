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
        Schema::table('portfolios', function (Blueprint $table) {
            $table->string('hero_prefix')->nullable()->default('A results-driven')->after('is_published');
            $table->string('hero_suffix', 1000)->nullable()->default('passionate about building products, crafting solutions, and continuous professional growth.')->after('hero_prefix');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('portfolios', function (Blueprint $table) {
            $table->dropColumn(['hero_prefix', 'hero_suffix']);
        });
    }
};
