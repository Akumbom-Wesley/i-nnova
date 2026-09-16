<?php

namespace App\Filament\Resources\CaseStudies\Tables;

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

class CaseStudiesTable
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

                TextColumn::make('institution')
                    ->searchable(),

                TextColumn::make('sector.name')
                    ->label('Sector')
                    ->placeholder('Unassigned')
                    ->toggleable(),

                TextColumn::make('product.name')
                    ->label('Product')
                    ->placeholder('None')
                    ->toggleable(),

                IconColumn::make('is_featured')
                    ->label('Home page')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('sector_id')
                    ->label('Sector')
                    ->options(fn () => Sector::query()->ordered()->get()->mapWithKeys(
                        fn (Sector $sector) => [$sector->id => $sector->name],
                    )),

                TernaryFilter::make('is_featured')
                    ->label('On the home page'),
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
