<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('team_members', function (Blueprint $table) {
            $table->id();
            // A person's name is not translated.
            $table->string('name');
            $table->string('slug')->unique();
            $table->json('role')->nullable();
            $table->json('credentials')->nullable();
            $table->json('bio')->nullable();
            $table->json('department')->nullable();
            // Keyed by network: linkedin, github, x, website. Not translated.
            $table->json('socials')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            // Drives the home page team teaser row.
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('team_members');
    }
};
