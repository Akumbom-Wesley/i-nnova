<?php

namespace App\Filament\Resources\CompanyValues\Schemas;

use App\Filament\Support\LocaleTabs;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CompanyValueForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                LocaleTabs::make(fn (string $locale) => [
                    TextInput::make("title.{$locale}")
                        ->label('Title')
                        ->required(LocaleTabs::isDefault($locale))
                        ->maxLength(120),

                    RichEditor::make("body.{$locale}")
                        ->label('Body')
                        ->columnSpanFull(),
                ]),

                SpatieMediaLibraryFileUpload::make('image')
                    ->collection('image')
                    ->image()
                    ->imageEditor(),

                TextInput::make('sort_order')
                    ->label('Order')
                    ->numeric()
                    ->default(0),
            ])
            ->columns(2);
    }
}
