<?php

namespace App\Filament\Resources\KickstarterMentors\Schemas;

use App\Filament\Support\LocaleTabs;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class KickstarterMentorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Mentor')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(120),

                        SpatieMediaLibraryFileUpload::make('photo')
                            ->collection('photo')
                            ->image()
                            ->imageEditor()
                            ->avatar(),

                        LocaleTabs::make(fn (string $locale) => [
                            TextInput::make("title.{$locale}")
                                ->label('Title')
                                ->required(LocaleTabs::isDefault($locale))
                                ->maxLength(120),

                            TextInput::make("credentials.{$locale}")
                                ->label('Credentials')
                                ->maxLength(160),

                            RichEditor::make("bio.{$locale}")
                                ->label('Bio')
                                ->columnSpanFull(),
                        ]),
                    ])
                    ->columns(2),

                Section::make('Links and placement')
                    ->schema([
                        TextInput::make('socials.linkedin')->label('LinkedIn')->url(),
                        TextInput::make('socials.github')->label('GitHub')->url(),
                        TextInput::make('socials.website')->label('Website')->url(),

                        TextInput::make('sort_order')
                            ->label('Order')
                            ->numeric()
                            ->default(0),
                    ])
                    ->columns(2),
            ]);
    }
}
