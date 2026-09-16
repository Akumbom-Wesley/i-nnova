<?php

namespace App\Filament\Resources\Leads\Tables;

use App\Enums\LeadStatus;
use App\Models\Lead;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class LeadsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Received')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),

                TextColumn::make('name')
                    ->searchable()
                    ->description(fn (Lead $record): string => $record->email)
                    // Unread rows carry weight so a full inbox still reads at a glance.
                    ->weight(fn (Lead $record): ?string => $record->read_at === null ? 'bold' : null),

                TextColumn::make('organisation')
                    ->placeholder('Not given')
                    ->toggleable(),

                TextColumn::make('subject')
                    ->placeholder('None')
                    ->limit(40)
                    ->toggleable(),

                TextColumn::make('status')
                    ->badge(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(LeadStatus::class),
            ])
            ->recordActions([
                EditAction::make()->label('Open'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
