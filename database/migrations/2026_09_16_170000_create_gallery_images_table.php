<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Photography: the internship running, the location, the team at work.
 *
 * Each row can carry either an uploaded file or a remote URL. The remote URL
 * exists so the layouts can be built and reviewed against real-looking
 * photographs now, and an editor can replace any of them with a real upload
 * later without a developer touching anything.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gallery_images', function (Blueprint $table) {
            $table->id();
            $table->string('placement')->default('about');
            $table->json('title')->nullable();
            $table->json('caption')->nullable();
            // Alt text is content, not decoration, so it is translatable and
            // edited alongside the image rather than derived from the title.
            $table->json('alt')->nullable();
            // Stand-in only. An uploaded file always wins over this.
            $table->string('external_url')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['placement', 'is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gallery_images');
    }
};
