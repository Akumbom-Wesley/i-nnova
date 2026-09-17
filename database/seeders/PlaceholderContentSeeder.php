<?php

namespace Database\Seeders;

use Database\Seeders\Placeholder\AboutSeeder;
use Database\Seeders\Placeholder\CatalogueSeeder;
use Database\Seeders\Placeholder\KickstarterSeeder;
use Database\Seeders\Placeholder\PhotographySeeder;
use Database\Seeders\Placeholder\SettingsSeeder;
use Database\Seeders\Placeholder\SocialProofSeeder;
use Illuminate\Database\Seeder;
use RuntimeException;

/**
 * Stand-in content so Sprints 2 and 3 can build real layouts before the real
 * copy and photography arrive. Every string here is placeholder, and Sprint 5
 * replaces the lot.
 *
 * Refuses to run in production: placeholder testimonials and client logos
 * must never reach a live site, and the surest guarantee is to make seeding
 * them there impossible.
 *
 * Safe to re-run. Every sub-seeder uses updateOrCreate and skips media it has
 * already attached.
 */
class PlaceholderContentSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->isProduction()) {
            throw new RuntimeException('PlaceholderContentSeeder must never run in production.');
        }

        $this->call([
            CatalogueSeeder::class,
            AboutSeeder::class,
            SocialProofSeeder::class,
            KickstarterSeeder::class,
            PhotographySeeder::class,
            SettingsSeeder::class,
        ]);
    }
}
