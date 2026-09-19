<?php

namespace App\Filament\Resources\TeamMembers\Tables;

use App\Models\TeamMember;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TeamMembersTable
{
    public static function configure(Table $table): Table
    {
        // Resolved once per table render rather than once per row, so marking
        // the home page three costs a single query no matter how many people
        // are listed.
        $onHomePage = TeamMember::query()
            ->ordered()
            ->limit(TeamMember::LEADERSHIP_COUNT)
            ->pluck('id')
            ->all();

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

                TextColumn::make('department')
                    ->placeholder('Unassigned')
                    ->toggleable(),

                // Which three the home page shows is decided by the order
                // above, so it is worth saying out loud. Otherwise dragging a
                // row has an effect on another page that nothing here admits.
                TextColumn::make('on_home_page')
                    ->label('')
                    ->badge()
                    ->color('success')
                    ->getStateUsing(fn (TeamMember $record): ?string => in_array($record->getKey(), $onHomePage, true)
                        ? 'On the home page'
                        : null),
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
