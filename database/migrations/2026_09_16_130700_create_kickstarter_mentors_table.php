<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kickstarter_mentors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->json('title')->nullable();
            $table->json('credentials')->nullable();
            $table->json('bio')->nullable();
            $table->json('socials')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kickstarter_mentors');
    }
};
