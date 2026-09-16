<?php

namespace Database\Seeders\Concerns;

use Database\Seeders\Support\PlaceholderImage;
use Illuminate\Database\Eloquent\Model;

trait AttachesPlaceholderImages
{
    /**
     * Idempotent: re-running a seeder must not pile up duplicate media.
     */
    protected function attachImage(
        Model $model,
        string $collection,
        string $label,
        int $width,
        int $height,
        string $tone = 'blue',
        int $index = 0,
    ): void {
        $fileName = str($label)->slug()->value() . '-' . $collection . ($index > 0 ? '-' . $index : '') . '.png';

        $isSingleFile = $model->getMediaCollection($collection)?->singleFile ?? false;

        if ($isSingleFile && $model->getMedia($collection)->isNotEmpty()) {
            return;
        }

        $alreadyAttached = $model->getMedia($collection)
            ->contains(fn ($media): bool => $media->file_name === $fileName);

        if ($alreadyAttached) {
            return;
        }

        $model->addMediaFromString(PlaceholderImage::png($label, $width, $height, $tone))
            ->usingFileName($fileName)
            ->usingName($label)
            ->toMediaCollection($collection);
    }
    /**
     * Attaches a real asset from disk rather than a generated placeholder.
     * Used for the genuine brand and institution artwork in public/images.
     */
    protected function attachFile(Model $model, string $collection, string $path, string $label): void
    {
        if (! is_file($path)) {
            return;
        }

        $fileName = basename($path);

        $alreadyAttached = $model->getMedia($collection)
            ->contains(fn ($media): bool => $media->file_name === $fileName);

        if ($alreadyAttached) {
            return;
        }

        $model->getMedia($collection)->each->delete();

        $model->addMedia($path)
            ->preservingOriginal()
            ->usingFileName($fileName)
            ->usingName($label)
            ->toMediaCollection($collection);
    }

}
