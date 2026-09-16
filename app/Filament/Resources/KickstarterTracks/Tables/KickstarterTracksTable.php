<?php

namespace App\Filament\Resources\KickstarterTracks\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class KickstarterTracksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                SpatieMediaLibraryImageColumn::make('image')
                    ->collection('image')
                    ->conversion('thumb')
                    ->label('')
                    ->width(64)
                    ->height(48),

                TextColumn::make('name')
                    ->searchable()
                    ->description(fn ($record): ?string => $record->summary),

                TextColumn::make('duration')
                    ->placeholder('Not set')
                    ->toggleable(),

                TextColumn::make('alumni_outcomes_count')
                    ->label('Alumni')
                    ->counts('alumniOutcomes'),

                IconColumn::make('is_active')
                    ->label('Running')
                    ->boolean(),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Currently running'),
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
