<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('project_images', function (Blueprint $table) {
            // Rename to file_path to support both images and PDFs
            $table->string('file_type')->default('image')->after('image_path'); // 'image' or 'pdf'
        });
    }

    public function down(): void
    {
        Schema::table('project_images', function (Blueprint $table) {
            $table->dropColumn('file_type');
        });
    }
};
