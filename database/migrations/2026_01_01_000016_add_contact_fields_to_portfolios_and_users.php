<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('portfolios', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('location');
            $table->string('whatsapp')->nullable()->after('phone');
            $table->string('linkedin_url')->nullable()->after('whatsapp');
            $table->string('github_url')->nullable()->after('linkedin_url');
            $table->string('behance_url')->nullable()->after('github_url');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->boolean('must_change_password')->default(false)->after('role');
        });
    }

    public function down(): void
    {
        Schema::table('portfolios', function (Blueprint $table) {
            $table->dropColumn(['phone', 'whatsapp', 'linkedin_url', 'github_url', 'behance_url']);
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('must_change_password');
        });
    }
};
