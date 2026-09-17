<?php

namespace App\Console\Commands;

use App\Models\SiteText;
use Illuminate\Console\Command;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Collects every fixed string the templates ask for and makes sure each one
 * has a row in the admin. Run it after adding strings to a view.
 */
class SyncSiteTexts extends Command
{
    protected $signature = 'site:sync-texts {--prune : Remove rows for strings no longer used in any view}';

    protected $description = 'Make every fixed string in the views editable in the admin';

    public function handle(): int
    {
        $found = $this->scan();

        if ($found === []) {
            $this->warn('No strings found. Is the views directory where it should be?');

            return self::FAILURE;
        }

        $defaults = $this->defaults();
        $created = 0;
        $regrouped = 0;

        foreach ($found as $key => $group) {
            $text = SiteText::firstOrNew(['key' => $key]);

            if (! $text->exists) {
                // Seeded with the current wording in both languages, so the
                // admin opens showing what the site says today rather than
                // a set of empty boxes.
                // The default locale has no language file, because its
                // strings are the keys. Seeding it with the key means the
                // admin opens showing the real wording rather than a blank.
                $text->value = collect($defaults)
                    ->map(fn (array $lines, string $locale): ?string => $locale === config('site.default_locale')
                        ? ($lines[$key] ?? $key)
                        : ($lines[$key] ?? null))
                    ->filter()
                    ->all();

                $created++;
            }

            if ($text->group !== $group) {
                $text->group = $group;

                if ($text->exists) {
                    $regrouped++;
                }
            }

            $text->save();
        }

        $this->info("{$created} added, {$regrouped} regrouped, " . count($found) . ' in use.');

        if ($this->option('prune')) {
            $removed = SiteText::query()->whereNotIn('key', array_keys($found))->delete();
            $this->info("{$removed} removed.");
        }

        SiteText::forgetCache();

        return self::SUCCESS;
    }

    /**
     * @return array<string, string> key to group
     */
    private function scan(): array
    {
        $root = resource_path('views');

        if (! is_dir($root)) {
            return [];
        }

        $found = [];

        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root));

        foreach ($files as $file) {
            if ($file->isDir() || ! str_ends_with($file->getFilename(), '.blade.php')) {
                continue;
            }

            $contents = (string) file_get_contents($file->getPathname());
            $group = $this->groupFor($file->getPathname(), $root);

            preg_match_all("/__\(\s*'((?:[^'\\\\]|\\\\.)*)'/", $contents, $matches);

            foreach ($matches[1] as $key) {
                $key = str_replace(["\\'", '\\\\'], ["'", '\\'], $key);

                // First file to use a string decides its group, so a shared
                // component does not drag a page string into "components".
                $found[$key] ??= $group;
            }
        }

        ksort($found);

        return $found;
    }

    private function groupFor(string $path, string $root): string
    {
        $relative = str_replace('\\', '/', substr($path, strlen($root) + 1));
        $parts = explode('/', $relative);
        $file = str_replace('.blade.php', '', array_pop($parts));

        return match ($parts[0] ?? '') {
            'pages' => 'Page: ' . ucfirst($parts[1] ?? $file),
            'sections' => 'Section: ' . ucfirst(str_replace('-', ' ', $file)),
            'partials' => 'Site: ' . ucfirst($file),
            'components' => 'Components',
            'errors' => 'Error pages',
            default => 'General',
        };
    }

    /**
     * @return array<string, array<string, string>>
     */
    private function defaults(): array
    {
        $defaults = [];

        foreach (array_keys(config('site.locales', [])) as $locale) {
            $path = lang_path($locale . '.json');

            $defaults[$locale] = is_file($path)
                ? (json_decode((string) file_get_contents($path), true) ?: [])
                : [];
        }

        // English has no file: the key is the string.
        return $defaults;
    }
}
