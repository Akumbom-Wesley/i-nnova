<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('case_studies', function (Blueprint $table) {
            $table->id();
            // The institution is a proper noun, so it is not translated.
            $table->string('institution');
            $table->string('slug')->unique();
            $table->foreignId('sector_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->json('summary')->nullable();
            $table->json('challenge')->nullable();
            $table->json('solution')->nullable();
            $table->json('results')->nullable();
            $table->json('quote')->nullable();
            $table->string('quote_attribution')->nullable();
            $table->json('quote_role')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('case_studies');
    }
};
