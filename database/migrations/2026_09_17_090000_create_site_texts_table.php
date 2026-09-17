<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Every fixed string on the site: section headings, eyebrows, leads, button
 * labels. Previously these lived only in the language files, so changing one
 * meant a developer and a deploy.
 *
 * A row here overrides the language file for its key. The files stay as the
 * defaults, which means an untouched string still works and a row can be
 * deleted to fall back rather than having to retype the original.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_texts', function (Blueprint $table) {
            $table->id();
            // The English source string, which is what __() looks up.
            $table->text('key');
            // Where it appears, so the admin can be grouped rather than a
            // single list of a hundred and fifty rows.
            $table->string('group')->default('general');
            $table->json('value')->nullable();
            $table->timestamps();

            $table->unique('key', 'site_texts_key_unique');
            $table->index('group');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_texts');
    }
};
