<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Where to find us. Coordinates rather than a pasted embed code, so the admin
 * cannot be used to inject arbitrary markup into the page, and so the map,
 * the directions link and the schema.org address all come from one source.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->decimal('map_latitude', 10, 7)->nullable()->after('address');
            $table->decimal('map_longitude', 10, 7)->nullable()->after('map_latitude');
            // Nullable: an empty zoom in the admin means the default, not a
            // failed save. The model decides what empty means.
            $table->unsignedTinyInteger('map_zoom')->nullable()->after('map_longitude');
            $table->boolean('map_is_visible')->default(true)->after('map_zoom');
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn(['map_latitude', 'map_longitude', 'map_zoom', 'map_is_visible']);
        });
    }
};
