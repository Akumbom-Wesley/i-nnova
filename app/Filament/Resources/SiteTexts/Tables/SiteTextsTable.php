<?php

namespace App\Filament\Resources\SiteTexts\Tables;

use App\Models\SiteText;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class SiteTextsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('key')
            ->defaultGroup('group')
            ->groups(['group'])
            ->paginated([25, 50, 100, 'all'])
            ->columns([
                TextColumn::make('value')
                    ->label('Text')
                    ->wrap()
                    ->searchable(['key'])
                    ->description(fn (SiteText $record): ?string => $record->value === $record->key
                        ? null
                        : 'Originally: ' . $record->key),

                TextColumn::make('group')
                    ->label('Section')
                    ->badge()
                    ->toggleable(),

                IconColumn::make('edited')
                    ->label('Edited')
                    ->boolean()
                    ->getStateUsing(fn (SiteText $record): bool => $record->value !== $record->key)
                    ->trueIcon('heroicon-o-pencil-square')
                    ->falseIcon('heroicon-o-minus-small')
                    ->trueColor('warning')
                    ->falseColor('gray'),
            ])
            ->filters([
                SelectFilter::make('group')
                    ->label('Section')
                    ->options(fn (): array => SiteText::query()
                        ->select('group')
                        ->distinct()
                        ->orderBy('group')
                        ->pluck('group', 'group')
                        ->all()),

                TernaryFilter::make('edited')
                    ->label('Changed from the original')
                    ->queries(
                        true: fn ($query) => $query->whereRaw('json_extract(value, ?) <> "key"', ['$."' . config('site.default_locale') . '"']),
                        false: fn ($query) => $query,
                        blank: fn ($query) => $query,
                    ),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                // No delete: a row missing here means the string silently falls
                // back to the template and nobody can find it to edit again.
                BulkActionGroup::make([]),
            ]);
    }
}
