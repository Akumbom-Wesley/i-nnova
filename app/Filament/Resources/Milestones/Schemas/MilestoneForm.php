<?php

namespace App\Filament\Resources\Milestones\Schemas;

use App\Filament\Support\LocaleTabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MilestoneForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('year')
                    ->required()
                    ->maxLength(9)
                    ->helperText('A year, or a range such as 2024 to 2025.'),

                TextInput::make('sort_order')
                    ->label('Order')
                    ->numeric()
                    ->default(0)
                    ->helperText('Oldest first reads best.'),

                LocaleTabs::make(fn (string $locale) => [
                    TextInput::make("title.{$locale}")
                        ->label('Title')
                        ->required(LocaleTabs::isDefault($locale))
                        ->maxLength(120),

                    Textarea::make("body.{$locale}")
                        ->label('What happened')
                        ->rows(3)
                        ->columnSpanFull(),
                ]),
            ])
            ->columns(2);
    }
}
