<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stats', function (Blueprint $table) {
            // manual keeps every existing row behaving as it did.
            $table->string('source')->default('manual')->after('context');
            // Shown after a counted value, so "6" can read as "6+".
            $table->string('suffix', 8)->nullable()->after('value');
        });

        Schema::table('site_settings', function (Blueprint $table) {
            $table->year('founded_year')->nullable()->after('vision');
        });
    }

    public function down(): void
    {
        Schema::table('stats', function (Blueprint $table) {
            $table->dropColumn(['source', 'suffix']);
        });

        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn('founded_year');
        });
    }
};
