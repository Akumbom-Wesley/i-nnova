<?php

namespace App\Filament\Resources\Partners\Schemas;

use App\Filament\Support\LocaleTabs;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PartnerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('The organisation')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(160),

                        TextInput::make('website_url')
                            ->label('Website')
                            ->url()
                            ->maxLength(255),

                        SpatieMediaLibraryFileUpload::make('logo')
                            ->collection('logo')
                            ->image()
                            ->helperText('Use the partner\'s own logo file. It is scaled to fit, never cropped.')
                            ->columnSpanFull(),

                        LocaleTabs::make(fn (string $locale) => [
                            TextInput::make("relationship.{$locale}")
                                ->label('Nature of the partnership')
                                ->maxLength(160)
                                ->helperText('Shown under a featured pairing, for example "Education partner".'),
                        ]),
                    ])
                    ->columns(2),

                Section::make('How it appears')
                    ->schema([
                        Toggle::make('is_verified')
                            ->label('Confirmed partnership')
                            ->helperText('Nothing appears on the site until this is on. A logo for a relationship that has not been confirmed must never be published.')
                            ->columnSpanFull(),


                        TextInput::make('sort_order')
                            ->label('Order')
                            ->numeric()
                            ->default(0),
                    ])
                    ->columns(2),
            ]);
    }
}
