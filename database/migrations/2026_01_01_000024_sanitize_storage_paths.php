<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Sanitize portfolios table paths
        DB::table('portfolios')->orderBy('id')->chunk(100, function ($rows) {
            foreach ($rows as $row) {
                $updates = [];
                if ($row->profile_picture_path) {
                    $path = ltrim($row->profile_picture_path, '/');
                    if (str_starts_with($path, 'storage/')) {
                        $updates['profile_picture_path'] = substr($path, 8);
                    }
                }
                if ($row->cv_path) {
                    $path = ltrim($row->cv_path, '/');
                    if (str_starts_with($path, 'storage/')) {
                        $updates['cv_path'] = substr($path, 8);
                    }
                }
                if (!empty($updates)) {
                    DB::table('portfolios')->where('id', $row->id)->update($updates);
                }
            }
        });

        // Sanitize projects table paths
        DB::table('projects')->orderBy('id')->chunk(100, function ($rows) {
            foreach ($rows as $row) {
                if ($row->cover_image_path) {
                    $path = ltrim($row->cover_image_path, '/');
                    if (str_starts_with($path, 'storage/')) {
                        DB::table('projects')->where('id', $row->id)->update([
                            'cover_image_path' => substr($path, 8),
                        ]);
                    }
                }
            }
        });

        // Sanitize project_images table paths
        DB::table('project_images')->orderBy('id')->chunk(100, function ($rows) {
            foreach ($rows as $row) {
                $updates = [];
                if ($row->image_path) {
                    $path = ltrim($row->image_path, '/');
                    if (str_starts_with($path, 'storage/')) {
                        $updates['image_path'] = substr($path, 8);
                    }
                }
                if ($row->thumbnail_path) {
                    $path = ltrim($row->thumbnail_path, '/');
                    if (str_starts_with($path, 'storage/')) {
                        $updates['thumbnail_path'] = substr($path, 8);
                    }
                }
                if (!empty($updates)) {
                    DB::table('project_images')->where('id', $row->id)->update($updates);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Down migration not strictly necessary as this is a one-way data sanitation
    }
};
