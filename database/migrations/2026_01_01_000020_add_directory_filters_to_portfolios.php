<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('portfolios', function (Blueprint $table) {
            $table->string('track')->nullable()->index();
            $table->string('specialization')->nullable()->index();
            $table->integer('graduation_year')->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::table('portfolios', function (Blueprint $table) {
            $table->dropColumn(['track', 'specialization', 'graduation_year']);
        });
    }
};
