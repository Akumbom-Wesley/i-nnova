<?php

namespace App\Filament\Resources\Stats\Schemas;

use App\Filament\Support\LocaleTabs;
use App\Models\Stat;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class StatForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('context')
                    ->label('Shown on')
                    ->options([
                        Stat::CONTEXT_SITE => 'Main site',
                        Stat::CONTEXT_KICKSTARTER => 'Kickstarter page',
                    ])
                    ->default(Stat::CONTEXT_SITE)
                    ->required(),

                TextInput::make('value')
                    ->required()
                    ->maxLength(40)
                    ->helperText('Free text so it can hold 40+, 98% or 3 years.'),

                LocaleTabs::make(fn (string $locale) => [
                    TextInput::make("label.{$locale}")
                        ->label('Label')
                        ->required(LocaleTabs::isDefault($locale))
                        ->maxLength(80),

                    TextInput::make("caption.{$locale}")
                        ->label('Caption')
                        ->maxLength(160),
                ]),

                TextInput::make('sort_order')
                    ->label('Order')
                    ->numeric()
                    ->default(0),
            ])
            ->columns(2);
    }
}
