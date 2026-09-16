<?php

namespace App\Filament\Resources\Sectors\Schemas;

use App\Filament\Support\LocaleTabs;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class SectorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                LocaleTabs::make(fn (string $locale) => [
                    TextInput::make("name.{$locale}")
                        ->label('Name')
                        ->required(LocaleTabs::isDefault($locale))
                        ->maxLength(80)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (Get $get, Set $set, ?string $state) use ($locale) {
                            if (! LocaleTabs::isDefault($locale) || blank($state) || filled($get('slug'))) {
                                return;
                            }

                            $set('slug', Str::slug($state));
                        }),
                ]),

                TextInput::make('slug')
                    ->required()
                    ->maxLength(80)
                    ->unique(ignoreRecord: true),

                TextInput::make('sort_order')
                    ->label('Order')
                    ->numeric()
                    ->default(0),
            ])
            ->columns(2);
    }
}
