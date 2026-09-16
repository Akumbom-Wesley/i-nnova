<?php

namespace App\Filament\Resources\KickstarterTracks\Schemas;

use App\Filament\Support\LocaleTabs;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class KickstarterTrackForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Track')
                    ->schema([
                        LocaleTabs::make(fn (string $locale) => [
                            TextInput::make("name.{$locale}")
                                ->label('Name')
                                ->required(LocaleTabs::isDefault($locale))
                                ->maxLength(120)
                                ->live(onBlur: true)
                                ->afterStateUpdated(function (Get $get, Set $set, ?string $state) use ($locale) {
                                    if (! LocaleTabs::isDefault($locale) || blank($state) || filled($get('slug'))) {
                                        return;
                                    }

                                    $set('slug', Str::slug($state));
                                }),

                            TextInput::make("duration.{$locale}")
                                ->label('Duration')
                                ->maxLength(80)
                                ->helperText('Free text, for example "12 weeks".'),

                            Textarea::make("summary.{$locale}")
                                ->label('Summary')
                                ->rows(3)
                                ->columnSpanFull(),

                            RichEditor::make("description.{$locale}")
                                ->label('Description')
                                ->columnSpanFull(),
                        ]),
                    ]),

                Section::make('Placement')
                    ->schema([
                        TextInput::make('slug')
                            ->required()
                            ->maxLength(120)
                            ->unique(ignoreRecord: true),

                        SpatieMediaLibraryFileUpload::make('image')
                            ->collection('image')
                            ->image()
                            ->imageEditor(),

                        TextInput::make('sort_order')
                            ->label('Order')
                            ->numeric()
                            ->default(0),

                        Toggle::make('is_active')
                            ->label('Currently running')
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }
}
