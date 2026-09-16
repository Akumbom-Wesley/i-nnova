<?php

namespace App\Filament\Resources\Stats\Tables;

use App\Models\Stat;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class StatsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->groups(['context'])
            ->columns([
                TextColumn::make('value')
                    ->weight('bold'),

                TextColumn::make('label')
                    ->searchable()
                    ->description(fn (Stat $record): ?string => $record->caption),

                TextColumn::make('context')
                    ->label('Shown on')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        Stat::CONTEXT_KICKSTARTER => 'Kickstarter page',
                        default => 'Main site',
                    }),
            ])
            ->filters([
                SelectFilter::make('context')
                    ->label('Shown on')
                    ->options([
                        Stat::CONTEXT_SITE => 'Main site',
                        Stat::CONTEXT_KICKSTARTER => 'Kickstarter page',
                    ]),
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
