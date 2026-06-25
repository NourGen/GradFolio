<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add cover image path to projects table
        Schema::table('projects', function (Blueprint $table) {
            $table->string('cover_image_path')->nullable()->after('sort_order');
        });

        // Add alt text and thumbnail path to project_images
        Schema::table('project_images', function (Blueprint $table) {
            $table->string('alt_text')->nullable()->after('caption');
            $table->string('thumbnail_path')->nullable()->after('alt_text');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('cover_image_path');
        });

        Schema::table('project_images', function (Blueprint $table) {
            $table->dropColumn(['alt_text', 'thumbnail_path']);
        });
    }
};
