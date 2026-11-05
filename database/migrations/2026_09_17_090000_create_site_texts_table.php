<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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

            $table->index('group');
        });

        // MySQL refuses a unique index on a whole TEXT column, which MariaDB
        // and SQLite accept. A 700 character prefix fits its 3072 byte key
        // limit in utf8mb4, and the longest key on the site is under 150.
        $connection = DB::connection();

        if ($connection->getDriverName() === 'mysql' && ! $connection->isMaria()) {
            DB::statement('ALTER TABLE site_texts ADD UNIQUE site_texts_key_unique (`key`(700))');
        } else {
            Schema::table('site_texts', fn (Blueprint $table) => $table->unique('key', 'site_texts_key_unique'));
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('site_texts');
    }
};
