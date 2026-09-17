<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The Kickstarter gallery takes video as well as photographs, because a lot of
 * what the programme produces is footage rather than stills.
 *
 * A row can carry a link to YouTube or Vimeo, or an uploaded file. Either way
 * it still needs its image, which is used as the poster frame.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gallery_images', function (Blueprint $table) {
            $table->string('video_url')->nullable()->after('external_url');
        });
    }

    public function down(): void
    {
        Schema::table('gallery_images', function (Blueprint $table) {
            $table->dropColumn('video_url');
        });
    }
};
