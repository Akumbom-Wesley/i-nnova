<?php

namespace App\Filament\Resources\Partners\Tables;

use App\Enums\PartnerLockup;
use App\Models\Partner;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class PartnersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                SpatieMediaLibraryImageColumn::make('logo')
                    ->collection('logo')
                    ->conversion('wall')
                    ->label('')
                    ->width(96)
                    ->height(48),

                TextColumn::make('name')
                    ->searchable()
                    ->description(fn (Partner $record): ?string => $record->relationship),

                TextColumn::make('appearance')
                    ->label('Appears as')
                    ->badge()
                    ->getStateUsing(fn (Partner $record): string => $record->is_featured
                        ? 'Paired lockup, ' . strtolower($record->lockup->getLabel())
                        : 'In the row')
                    ->color(fn (Partner $record): string => $record->is_featured ? 'primary' : 'gray'),

                IconColumn::make('is_verified')
                    ->label('Confirmed')
                    ->boolean(),
            ])
            ->filters([
                TernaryFilter::make('is_verified')
                    ->label('Confirmed partnership'),

                TernaryFilter::make('is_featured')
                    ->label('Shown as a pairing'),
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
