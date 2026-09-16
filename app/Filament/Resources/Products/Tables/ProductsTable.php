<?php

namespace App\Filament\Resources\Products\Tables;

use App\Enums\ProductStatus;
use App\Models\Product;
use App\Models\Sector;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                SpatieMediaLibraryImageColumn::make('logo')
                    ->collection('logo')
                    ->conversion('thumb')
                    ->label('')
                    ->width(48)
                    ->height(48),

                TextColumn::make('name')
                    ->searchable()
                    ->description(fn (Product $record): ?string => $record->tagline),

                TextColumn::make('sector.name')
                    ->label('Sector')
                    ->placeholder('Unassigned')
                    ->toggleable(),

                TextColumn::make('status')
                    ->badge(),

                TextColumn::make('launch_date')
                    ->label('Launch')
                    ->date('M Y')
                    ->placeholder('No date')
                    ->toggleable(),

                IconColumn::make('is_featured')
                    ->label('Featured')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(ProductStatus::class),

                SelectFilter::make('sector_id')
                    ->label('Sector')
                    ->options(fn () => Sector::query()->ordered()->get()->mapWithKeys(
                        fn (Sector $sector) => [$sector->id => $sector->name],
                    )),

                TernaryFilter::make('is_featured')
                    ->label('Featured'),
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
