<?php

namespace App\Filament\Resources\Testimonials\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class TestimonialsTable
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

                TextColumn::make('person_name')
                    ->label('Person')
                    ->searchable()
                    ->description(fn ($record): ?string => $record->organisation),

                TextColumn::make('quote')
                    ->limit(60)
                    ->color('gray'),

                TextColumn::make('product.name')
                    ->label('Product')
                    ->placeholder('General')
                    ->toggleable(),

                IconColumn::make('is_featured')
                    ->label('Home page')
                    ->boolean(),
            ])
            ->filters([
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
