<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * One table serves both the site stat blocks and the Kickstarter programme
 * stats. "context" keeps them apart. Named context rather than group because
 * GROUP is a reserved word in MySQL.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stats', function (Blueprint $table) {
            $table->id();
            $table->string('context')->default('site');
            $table->json('label');
            // Kept as a string so it can hold "40+", "98%" or "3 years".
            $table->string('value');
            $table->json('caption')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['context', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stats');
    }
};
