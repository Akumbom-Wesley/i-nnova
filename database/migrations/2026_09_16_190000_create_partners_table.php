<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Partner organisations. Distinct from clients: a client runs our software,
 * a partner is an organisation we work alongside, and the brand guide gives
 * partnerships their own co-branding rules (Section 4).
 *
 * is_verified defaults to false and the front end only ever renders verified
 * rows. The previous site carried partner logos for relationships that did not
 * exist, which is on the "do not repeat" list, so an unconfirmed partner
 * cannot reach a page even by accident.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partners', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('website_url')->nullable();
            $table->json('relationship')->nullable();
            $table->string('lockup')->default('horizontal');
            $table->boolean('is_verified')->default(false);
            // A featured partner gets the full co-branded lockup from the
            // brand guide rather than a place in the row.
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_verified', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partners');
    }
};
