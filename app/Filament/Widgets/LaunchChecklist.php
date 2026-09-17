<?php

namespace App\Filament\Widgets;

use App\Models\Client;
use App\Models\GalleryImage;
use App\Models\Product;
use App\Models\SiteSetting;
use App\Models\TeamMember;
use App\Models\Testimonial;
use Filament\Widgets\Widget;

/**
 * What is still outstanding before the site is fit to launch.
 *
 * Each row is a real query rather than a hand kept list, so it cannot drift
 * out of date. An item disappears the moment it is actually done.
 */
class LaunchChecklist extends Widget
{
    protected string $view = 'filament.widgets.launch-checklist';

    // Rendered with the page rather than fetched afterwards. The dashboard
    // is the first thing seen on signing in, and a row of empty cards that
    // fill a moment later reads as something being broken.
    protected static bool $isLazy = false;

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getItems(): array
    {
        $settings = SiteSetting::instance();

        $items = [
            [
                'label' => 'Photographs still using a stand-in',
                'detail' => 'Upload the real photograph and it takes over automatically.',
                'count' => GalleryImage::query()->whereDoesntHave('media')->whereNotNull('external_url')->count(),
                'url' => route('filament.admin.resources.gallery-images.index'),
            ],
            [
                'label' => 'Clients awaiting verification',
                'detail' => 'Unverified clients never render on the site.',
                'count' => Client::query()->where('is_verified', false)->count(),
                'url' => route('filament.admin.resources.clients.index'),
            ],
            [
                'label' => 'Testimonials without a photograph',
                'detail' => 'A quote with a face behind it carries more weight.',
                'count' => Testimonial::query()->whereDoesntHave('media')->count(),
                'url' => route('filament.admin.resources.testimonials.index'),
            ],
            [
                'label' => 'Team members without a portrait',
                'detail' => 'The About page shows an empty circle until one is added.',
                'count' => TeamMember::query()->whereDoesntHave('media')->count(),
                'url' => route('filament.admin.resources.team-members.index'),
            ],
            [
                'label' => 'Products with no cover image',
                'detail' => 'Product cards fall back to plain text without one.',
                'count' => Product::query()->whereDoesntHave('media', fn ($q) => $q->where('collection_name', 'cover'))->count(),
                'url' => route('filament.admin.resources.products.index'),
            ],
        ];

        // Contact details are the thing most likely to ship wrong, so they are
        // checked individually rather than as one row.
        $missingContact = collect([
            'email' => $settings->contact_email,
            'phone' => $settings->contact_phone,
            'WhatsApp number' => $settings->whatsapp_number,
            'address' => $settings->address,
        ])->filter(fn ($value): bool => blank($value))->keys();

        if ($missingContact->isNotEmpty()) {
            $items[] = [
                'label' => 'Contact details not set',
                'detail' => 'Missing: ' . $missingContact->implode(', ') . '.',
                'count' => $missingContact->count(),
                'url' => route('filament.admin.pages.manage-site-settings'),
            ];
        }

        return collect($items)
            ->filter(fn (array $item): bool => $item['count'] > 0)
            ->values()
            ->all();
    }
}
