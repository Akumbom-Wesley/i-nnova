<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Single row, edited through a dedicated Filament page rather than a resource.
 * Contact details start null on purpose: the old site shipped a placeholder
 * phone number and a mismatched email domain, both on the "do not repeat" list.
 * Nothing placeholder gets seeded here.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();

            $table->json('hero_heading')->nullable();
            $table->json('hero_subheading')->nullable();
            $table->json('hero_cta_label')->nullable();
            $table->string('hero_cta_url')->nullable();

            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('whatsapp_number')->nullable();
            $table->json('address')->nullable();

            $table->json('socials')->nullable();
            $table->string('kickstarter_url')->nullable();

            $table->json('seo_title')->nullable();
            $table->json('seo_description')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
