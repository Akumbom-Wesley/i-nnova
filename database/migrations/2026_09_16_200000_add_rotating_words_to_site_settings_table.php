<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The words that cycle at the end of "Transforming communities, empowering".
 * Editable, because the line is only interesting while the words are the
 * right ones, and that is a copy decision rather than a code one.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->json('about_rotating_words')->nullable()->after('about_heading');
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn('about_rotating_words');
        });
    }
};
