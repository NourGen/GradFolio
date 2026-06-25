<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DatabaseMigrationVerificationTest extends TestCase
{
    /** @test */
    public function test_all_expected_database_tables_exist_after_migration()
    {
        $this->artisan('migrate');

        $expectedTables = [
            'users',
            'portfolios',
            'skills',
            'social_links',
            'projects',
            'project_images',
            'portfolio_views',
            'cv_downloads',
            'project_clicks',
        ];

        foreach ($expectedTables as $table) {
            $this->assertTrue(
                Schema::hasTable($table),
                "Database table '{$table}' is missing."
            );
        }
    }

    /** @test */
    public function test_core_table_columns_are_correct()
    {
        $this->artisan('migrate');

        // Check users columns
        $this->assertTrue(Schema::hasColumn('users', 'is_suspended'));
        $this->assertTrue(Schema::hasColumn('users', 'must_change_password'));
        $this->assertTrue(Schema::hasColumn('users', 'role'));

        // Check portfolios columns
        $this->assertTrue(Schema::hasColumn('portfolios', 'is_verified'));
        $this->assertTrue(Schema::hasColumn('portfolios', 'hero_prefix'));
        $this->assertTrue(Schema::hasColumn('portfolios', 'hero_suffix'));
        $this->assertTrue(Schema::hasColumn('portfolios', 'track'));

        // Check cv_downloads columns
        $this->assertTrue(Schema::hasColumn('cv_downloads', 'ip_hash'));

        // Check project_clicks columns
        $this->assertTrue(Schema::hasColumn('project_clicks', 'project_id'));
    }
}
