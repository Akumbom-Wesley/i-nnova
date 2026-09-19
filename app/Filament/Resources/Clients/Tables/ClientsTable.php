<?php

namespace App\Filament\Resources\Clients\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ClientsTable
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
                    ->width(80)
                    ->height(40),

                TextColumn::make('name')
                    ->searchable(),

                TextColumn::make('website_url')
                    ->label('Website')
                    ->placeholder('None')
                    ->url(fn ($record): ?string => $record->website_url)
                    ->openUrlInNewTab()
                    ->toggleable(),

                TextColumn::make('products.name')
                    ->label('Runs')
                    ->badge()
                    ->placeholder('None yet')
                    ->toggleable(),

                IconColumn::make('is_verified')
                    ->label('Verified')
                    ->boolean(),

                // Whether this client has a page of its own. It is the summary
                // that decides, so it is worth showing rather than leaving
                // people to guess why some appear under Our work and some do not.
                IconColumn::make('has_a_page')
                    ->label('Has a page')
                    ->boolean()
                    ->getStateUsing(fn ($record): bool => $record->isTold()),
            ])
            ->filters([
                TernaryFilter::make('is_verified')
                    ->label('Verified'),

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
