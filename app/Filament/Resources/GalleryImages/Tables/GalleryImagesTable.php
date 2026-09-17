<?php

namespace App\Filament\Resources\GalleryImages\Tables;

use App\Enums\GalleryPlacement;
use App\Models\GalleryImage;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class GalleryImagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            // Photography is the one set that grows without limit, and the
            // rows are tall because each carries a preview. Twelve fills
            // a screen; reordering stays paged rather than loading every
            // photograph the company has ever taken into one list.
            ->paginated([12, 24, 48, 'all'])
            ->defaultPaginationPageOption(12)
            ->paginatedWhileReordering()
            ->groups(['placement'])
            ->columns([
                // Reads the same accessor the front end does, so the admin
                // shows exactly what a visitor would see.
                ImageColumn::make('preview')
                    ->label('')
                    ->getStateUsing(fn (GalleryImage $record): ?string => $record->displayUrl())
                    ->width(96)
                    ->height(64),

                TextColumn::make('title')
                    ->placeholder('Untitled')
                    ->searchable()
                    ->description(fn (GalleryImage $record): ?string => $record->caption),

                TextColumn::make('placement')
                    ->badge(),

                TextColumn::make('source')
                    ->label('Source')
                    ->badge()
                    ->getStateUsing(fn (GalleryImage $record): string => $record->isPlaceholder() ? 'Stand-in' : 'Uploaded')
                    ->color(fn (string $state): string => $state === 'Stand-in' ? 'warning' : 'success'),

                IconColumn::make('is_active')
                    ->label('Shown')
                    ->boolean(),

                // Editable in the list, because choosing the three on show is
                // a comparison between rows rather than a decision you make
                // one record at a time.
                ToggleColumn::make('is_featured')
                    ->label('Featured'),
            ])
            ->filters([
                SelectFilter::make('placement')
                    ->options(GalleryPlacement::class),

                TernaryFilter::make('is_active')
                    ->label('Shown on the site'),

                TernaryFilter::make('is_featured')
                    ->label('Featured on the page'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
