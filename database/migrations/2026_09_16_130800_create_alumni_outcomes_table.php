<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alumni_outcomes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->json('role')->nullable();
            $table->string('organisation')->nullable();
            $table->json('outcome')->nullable();
            $table->json('quote')->nullable();
            $table->foreignId('kickstarter_track_id')->nullable()->constrained()->nullOnDelete();
            $table->year('cohort_year')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alumni_outcomes');
    }
};
