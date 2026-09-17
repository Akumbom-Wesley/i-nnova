<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        if (app()->isProduction()) {
            // Nothing in here belongs on a live site: the admin below has a
            // known password and the rest is placeholder content. Create the
            // real first account with `php artisan make:filament-user`.
            $this->command?->warn('Production detected. Skipping the development admin and all placeholder content.');

            return;
        }

        User::firstOrCreate(
            ['email' => 'admin@i-nnovacmr.com'],
            [
                'name' => 'I-NNOVA Admin',
                // Development only. Must be changed before anything is deployed.
                'password' => 'innova-dev-2026',
            ],
        );

        $this->call(PlaceholderContentSeeder::class);

        // Makes every fixed string in the templates editable in the admin.
        Artisan::call('site:sync-texts');
    }
}
