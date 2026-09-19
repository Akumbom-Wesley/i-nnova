<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * One control, not two.
 *
 * The home page used to show whoever was flagged featured, in the order set
 * in the admin. That is two settings that can disagree: a member could be
 * ordered first and still be missing from the home page, with nothing on the
 * screen explaining why. The order alone now decides, and the first three in
 * it are the ones the home page shows.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('team_members', function (Blueprint $table) {
            $table->dropColumn('is_featured');
        });
    }

    public function down(): void
    {
        Schema::table('team_members', function (Blueprint $table) {
            $table->boolean('is_featured')->default(false);
        });
    }
};
