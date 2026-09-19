<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * A case study was never a separate thing. It was a client, written up.
 *
 * PAXHI and SAHIK are clients running EduTrust, exactly as other clients run
 * the other products. Keeping both models meant the same institution could
 * exist twice, once as a logo and once as a story, with nothing keeping the
 * two in step.
 *
 * Clients take on what made a case study worth having: a slug of its own, a
 * sector, the written sections, and the products they actually run. The
 * products are a pivot rather than a column, because a school can run the
 * school system and the POS at once and forcing that into one column would
 * mean inventing a second client row for the same institution.
 *
 * The case_studies table is dropped rather than copied across, by decision:
 * the two rows in it are being re-entered by hand.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('name');
            $table->foreignId('sector_id')->nullable()->after('slug')->constrained()->nullOnDelete();

            $table->json('summary')->nullable();
            $table->json('challenge')->nullable();
            $table->json('solution')->nullable();
            $table->json('results')->nullable();

            $table->json('quote')->nullable();
            $table->string('quote_attribution')->nullable();
            $table->json('quote_role')->nullable();

            // Which clients lead the home page, as distinct from is_verified,
            // which is whether they may be shown at all.
            $table->boolean('is_featured')->default(false);
        });

        // Existing rows predate the slug, so give them one.
        foreach (DB::table('clients')->select('id', 'name')->get() as $client) {
            DB::table('clients')
                ->where('id', $client->id)
                ->update(['slug' => Str::slug($client->name) . '-' . $client->id]);
        }

        // Unique but nullable, deliberately. Only a client with a story told
        // about it has a page, so most rows never need a slug, and requiring
        // one would mean inventing an address for a logo on a wall. The model
        // fills it in on save where it can, but that cannot be relied on for
        // a NOT NULL column: seeders run inside WithoutModelEvents, which
        // mutes exactly that hook. Several NULLs coexist happily under a
        // unique index on both MySQL and SQLite.
        Schema::table('clients', function (Blueprint $table) {
            $table->unique('slug');
        });

        Schema::create('client_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['client_id', 'product_id']);
        });

        Schema::dropIfExists('case_studies');
    }

    public function down(): void
    {
        Schema::dropIfExists('client_product');

        Schema::table('clients', function (Blueprint $table) {
            $table->dropConstrainedForeignId('sector_id');
            $table->dropUnique(['slug']);
            $table->dropColumn([
                'slug', 'summary', 'challenge', 'solution', 'results',
                'quote', 'quote_attribution', 'quote_role', 'is_featured',
            ]);
        });

        // Deliberately not recreating case_studies. Rolling this back restores
        // the shape of the clients table, not a model the project decided
        // against, and the rows in it were never copied anywhere.
    }
};
