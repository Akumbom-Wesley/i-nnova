<?php

namespace App\Filament\Resources\Clients\Schemas;

use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ClientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
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
                    ->columnSpanFull(),

                Toggle::make('is_verified')
                    ->label('Verified relationship')
                    ->helperText('Only verified clients are ever rendered on the site. Leave this off until the relationship is confirmed.')
                    ->columnSpanFull(),

                TextInput::make('sort_order')
                    ->label('Order')
                    ->numeric()
                    ->default(0),
            ])
            ->columns(2);
    }
}
