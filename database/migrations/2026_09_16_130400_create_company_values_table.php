<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Named company_values because "values" is a reserved word in MySQL.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_values', function (Blueprint $table) {
            $table->id();
            $table->json('title');
            $table->json('body')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_values');
    }
};
