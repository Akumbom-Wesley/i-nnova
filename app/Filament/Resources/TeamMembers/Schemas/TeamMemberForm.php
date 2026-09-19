<?php

namespace App\Filament\Resources\TeamMembers\Schemas;

use App\Filament\Support\LocaleTabs;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class TeamMemberForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Person')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(120)
                            ->helperText('Not translated. A person is called the same thing in both languages.')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                if (blank($state) || filled($get('slug'))) {
                                    return;
                                }

                                $set('slug', Str::slug($state));
                            }),

                        TextInput::make('slug')
                            ->required()
                            ->maxLength(120)
                            ->unique(ignoreRecord: true),

                        SpatieMediaLibraryFileUpload::make('photo')
                            ->collection('photo')
                            ->image()
                            ->imageEditor()
                            ->avatar()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Role')
                    ->schema([
                        LocaleTabs::make(fn (string $locale) => [
                            TextInput::make("role.{$locale}")
                                ->label('Role')
                                ->required(LocaleTabs::isDefault($locale))
                                ->maxLength(120),

                            TextInput::make("department.{$locale}")
                                ->label('Department')
                                ->maxLength(80)
                                ->helperText('Groups the full team section on the About page.'),

                            TextInput::make("credentials.{$locale}")
                                ->label('Credentials')
                                ->maxLength(160),

                            RichEditor::make("bio.{$locale}")
                                ->label('Bio')
                                ->columnSpanFull(),
                        ]),
                    ]),

                Section::make('Links and placement')
                    ->schema([
                        TextInput::make('socials.linkedin')->label('LinkedIn')->url()->prefixIcon('heroicon-o-link'),
                        TextInput::make('socials.github')->label('GitHub')->url()->prefixIcon('heroicon-o-link'),
                        TextInput::make('socials.x')->label('X')->url()->prefixIcon('heroicon-o-link'),
                        TextInput::make('socials.website')->label('Website')->url()->prefixIcon('heroicon-o-link'),

                        TextInput::make('sort_order')
                            ->label('Order')
                            ->numeric()
                            ->default(0)
                            ->helperText('Lowest first. This order is used on the About page and on the home page, and the first three people in it are the ones the home page shows.'),
                    ])
                    ->columns(2),
            ]);
    }
}
