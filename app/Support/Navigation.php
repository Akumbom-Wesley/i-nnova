<?php

namespace App\Support;

use App\Enums\GalleryPlacement;
use App\Models\GalleryImage;
use App\Models\KickstarterTrack;
use App\Models\ProcessStep;
use App\Models\Product;
use App\Models\TeamMember;
use Illuminate\Support\Collection;

/**
 * The header menu, grouped rather than flat.
 *
 * Products is filled from what is actually published, so a new product appears
 * in the menu without anyone editing a template. The rest are fixed groups
 * pointing at pages and sections that already exist.
 */
class Navigation
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public static function menu(): array
    {
        return [
            [
                'label' => __('Home'),
                'url' => route('home'),
                'children' => [],
            ],
            [
                'label' => __('Products'),
                'url' => route('products.index'),
                'children' => static::products(),
            ],
            [
                'label' => __('Kickstarter'),
                'url' => route('kickstarter'),
                // Anchors are only offered when the section they point at is
                // actually on the page. Every one of these sections is
                // conditional, so linking unconditionally would give a menu
                // full of links that go nowhere on a quiet site.
                'children' => array_values(array_filter([
                    ['label' => __('The programme'), 'url' => route('kickstarter')],
                    static::anchor(
                        KickstarterTrack::query()->where('is_active', true)->exists(),
                        __('Accelerator tracks'),
                        route('kickstarter') . '#tracks',
                    ),
                    static::anchor(
                        GalleryImage::query()->placedOn(GalleryPlacement::Kickstarter)->exists(),
                        __('Gallery'),
                        route('kickstarter') . '#gallery',
                    ),
                ])),
            ],
            [
                'label' => __('Company'),
                'url' => route('about'),
                'children' => array_values(array_filter([
                    ['label' => __('About'), 'url' => route('about')],
                    ['label' => __('Our work'), 'url' => route('work.index')],
                    static::anchor(
                        ProcessStep::query()->exists(),
                        __('How we work'),
                        route('about') . '#how-we-work',
                    ),
                    static::anchor(
                        TeamMember::query()->exists(),
                        __('The team'),
                        route('about') . '#team',
                    ),
                    ['label' => __('Contact'), 'url' => route('contact')],
                ])),
            ],
        ];
    }

    /**
     * An anchor item, or null when the section it points at is not there.
     *
     * @return array<string, string>|null
     */
    private static function anchor(bool $exists, string $label, string $url): ?array
    {
        return $exists ? ['label' => $label, 'url' => $url] : null;
    }

    /**
     * @return array<int, array<string, string>>
     */
    private static function products(): array
    {
        $products = Product::query()
            ->live()
            ->ordered()
            ->get(['id', 'name', 'slug'])
            ->map(fn (Product $product): array => [
                'label' => (string) $product->name,
                'url' => route('products.show', $product),
            ])
            ->all();

        // The index goes last, so the menu reads as the things themselves
        // followed by a way to see all of them.
        $products[] = ['label' => __('All products'), 'url' => route('products.index')];

        return $products;
    }

    /**
     * True when this item, or anything under it, is the page being shown.
     */
    public static function isCurrent(array $item): bool
    {
        $current = url()->current();

        return Collection::make([$item['url']])
            ->merge(Collection::make($item['children'] ?? [])->pluck('url'))
            ->map(fn (string $url): string => (string) strtok($url, '#'))
            ->contains($current);
    }
}
