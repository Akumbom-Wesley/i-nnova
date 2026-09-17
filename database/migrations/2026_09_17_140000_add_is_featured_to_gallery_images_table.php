<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Which photographs get the shop window.
 *
 * The Kickstarter page shows a short selection and links to the full gallery,
 * so every row needs to say whether it is one of the few or one of the many.
 * Nullable would be wrong here: an untouched row is simply not featured.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gallery_images', function (Blueprint $table) {
            $table->boolean('is_featured')->default(false)->after('is_active');

            // The front end asks for "featured, in order, on this placement"
            // on every page load, and the gallery page pages through the same
            // set. Worth an index once the photography stops being a handful.
            $table->index(['placement', 'is_featured', 'sort_order'], 'gallery_images_placement_featured_idx');
        });
    }

    public function down(): void
    {
        Schema::table('gallery_images', function (Blueprint $table) {
            $table->dropIndex('gallery_images_placement_featured_idx');
            $table->dropColumn('is_featured');
        });
    }
};
