<?php

namespace Tests\Feature;

use App\Models\TeamMember;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

/**
 * Uploads must land somewhere the web can serve.
 *
 * Filament's own default upload disk is env('FILESYSTEM_DISK', 'local'), and
 * every upload in the admin inherits it, Media Library's included. On a
 * deployment where that is 'local', an image saves perfectly and then never
 * loads: the record is right, the file is on disk, and the browser spins on an
 * address outside the web root. It happened in production, and the only way to
 * notice is to look at the disk column.
 */
class UploadDiskTest extends TestCase
{
    use RefreshDatabase;

    public function test_filament_uploads_to_a_disk_the_web_can_serve(): void
    {
        $this->assertSame('public', config('filament.default_filesystem_disk'));
    }

    public function test_the_upload_disk_is_not_tied_to_the_frameworks_default(): void
    {
        // Livewire's temporary uploads follow filesystems.default, and a half
        // uploaded, unvalidated file should not be publicly readable. So these
        // two are deliberately allowed to differ, and coupling them back
        // together would be a regression even though it would look tidier.
        config()->set('filesystems.default', 'local');

        $this->assertSame('public', config('filament.default_filesystem_disk'));
    }

    public function test_media_library_and_filament_agree(): void
    {
        // Two packages each with their own default. They disagreed in
        // production, and Filament's won.
        $this->assertSame(
            config('media-library.disk_name'),
            config('filament.default_filesystem_disk'),
            'Media Library and Filament must target the same disk, or which one wins depends on how the file was attached.',
        );
    }

    public function test_an_attached_image_is_reachable_over_http(): void
    {
        $member = TeamMember::create(['name' => 'Ada Example', 'slug' => 'ada-example']);

        $member->addMedia(UploadedFile::fake()->image('portrait.jpg', 400, 400))
            ->toMediaCollection('photo');

        $media = $member->getFirstMedia('photo');

        $this->assertSame('public', $media->disk, 'An upload landed on a disk that is not web served.');

        // storage:link maps /storage to the public disk, so the generated URL
        // has to sit under it or nothing will ever serve the file. The stranded
        // upload in production produced a path outside it.
        $this->assertStringContainsString('/storage/', $media->getUrl());
    }
}
