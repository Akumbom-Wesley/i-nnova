<?php

namespace App\Filament\Resources\AlumniOutcomes\Tables;

use App\Models\KickstarterTrack;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AlumniOutcomesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                SpatieMediaLibraryImageColumn::make('photo')
                    ->collection('photo')
                    ->conversion('thumb')
                    ->circular()
                    ->label(''),

                TextColumn::make('name')
                    ->searchable()
                    ->description(fn ($record): ?string => $record->role),

                TextColumn::make('organisation')
                    ->label('Now at')
                    ->placeholder('Not set'),

                TextColumn::make('track.name')
                    ->label('Track')
                    ->placeholder('Unassigned')
                    ->toggleable(),

                TextColumn::make('cohort_year')
                    ->label('Cohort')
                    ->placeholder('Not set')
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('kickstarter_track_id')
                    ->label('Track')
                    ->options(fn () => KickstarterTrack::query()->ordered()->get()->mapWithKeys(
                        fn (KickstarterTrack $track) => [$track->id => $track->name],
                    )),
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
