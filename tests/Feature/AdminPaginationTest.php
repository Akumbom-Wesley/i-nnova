<?php

namespace Tests\Feature;

use App\Enums\GalleryPlacement;
use App\Filament\Resources\GalleryImages\GalleryImageResource;
use App\Filament\Resources\GalleryImages\Pages\ListGalleryImages;
use App\Models\GalleryImage;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Tables\Table;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Long lists in the admin must page.
 *
 * Filament paginates by default but stops while a table is being reordered,
 * which is the one time a long list really hurts: dragging a photograph into
 * place would otherwise render every row first.
 */
class AdminPaginationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::create([
            'name' => 'Editor',
            'email' => 'editor@example.test',
            'password' => 'password',
        ]));

        Filament::setCurrentPanel('admin');
    }

    public function test_every_admin_table_pages_even_while_being_reordered(): void
    {
        $table = $this->galleryTable();

        $this->assertTrue($table->isPaginated());
        $this->assertTrue($table->isPaginatedWhileReordering());
    }

    public function test_the_page_size_starts_somewhere_useful(): void
    {
        $table = $this->galleryTable();

        // Five rows of photography is a page of scrolling to see almost
        // nothing, which is the default Filament would otherwise pick.
        $this->assertGreaterThanOrEqual(12, $table->getDefaultPaginationPageOption());
        $this->assertContains(12, $table->getPaginationPageOptions());
    }

    public function test_a_long_list_does_not_render_every_row(): void
    {
        foreach (range(1, 40) as $index) {
            GalleryImage::create([
                'placement' => GalleryPlacement::Kickstarter,
                'external_url' => "https://example.test/photo-{$index}.jpg",
                'title' => ['en' => "Photograph {$index}"],
                'sort_order' => $index,
                'is_active' => true,
            ]);
        }

        $html = $this->get(GalleryImageResource::getUrl('index'))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('Photograph 1', $html);
        $this->assertStringNotContainsString('Photograph 40', $html);
    }

    /**
     * The table as the real list page builds it, so this tests what the panel
     * actually renders rather than a table assembled here.
     */
    private function galleryTable(): Table
    {
        return Livewire::test(ListGalleryImages::class)->instance()->getTable();
    }
}
