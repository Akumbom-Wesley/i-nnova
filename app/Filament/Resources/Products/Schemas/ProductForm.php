<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Enums\ProductStatus;
use App\Filament\Support\LocaleTabs;
use App\Models\Sector;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Content')
                    ->description('Everything a visitor reads, in each language.')
                    ->schema([
                        LocaleTabs::make(fn (string $locale) => [
                            TextInput::make("name.{$locale}")
                                ->label('Name')
                                ->required(LocaleTabs::isDefault($locale))
                                ->maxLength(120)
                                ->live(onBlur: true)
                                ->afterStateUpdated(function (Get $get, Set $set, ?string $state) use ($locale) {
                                    // Only the default locale drives the slug, and only while
                                    // the slug is still empty, so renaming a live product
                                    // never silently breaks its URL.
                                    if (! LocaleTabs::isDefault($locale) || blank($state) || filled($get('slug'))) {
                                        return;
                                    }

                                    $set('slug', Str::slug($state));
                                }),

                            TextInput::make("tagline.{$locale}")
                                ->label('Tagline')
                                ->maxLength(160)
                                ->helperText('One line. Shown on cards and the products index.'),

                            RichEditor::make("description.{$locale}")
                                ->label('Description')
                                ->columnSpanFull(),

                            TagsInput::make("features.{$locale}")
                                ->label('Features')
                                ->helperText('Press Enter after each feature.')
                                ->columnSpanFull(),
                        ]),
                    ]),

                Section::make('Placement')
                    ->schema([
                        TextInput::make('slug')
                            ->required()
                            ->maxLength(120)
                            ->unique(ignoreRecord: true)
                            ->helperText('Used in the URL. Changing it on a live product breaks existing links.'),

                        Select::make('sector_id')
                            ->label('Sector')
                            ->options(fn () => Sector::query()->ordered()->get()->mapWithKeys(
                                fn (Sector $sector) => [$sector->id => $sector->name],
                            ))
                            ->searchable()
                            ->preload(),

                        TextInput::make('sort_order')
                            ->label('Order')
                            ->numeric()
                            ->default(0)
                            ->helperText('Lower numbers appear first.'),

                        Toggle::make('is_featured')
                            ->label('Feature on the home page'),
                    ])
                    ->columns(2),

                Section::make('Status')
                    ->description('A product only reads as shipped once it is marked live.')
                    ->schema([
                        Select::make('status')
                            ->options(ProductStatus::class)
                            ->default(ProductStatus::ComingSoon)
                            ->required()
                            ->live(),

                        DatePicker::make('launch_date')
                            ->label('Expected launch')
                            ->helperText('Optional. Leave empty if there is no date worth committing to.')
                            ->visible(fn (Get $get) => $get('status') === ProductStatus::ComingSoon->value),

                        TextInput::make('website_url')
                            ->label('Product website')
                            ->url()
                            ->maxLength(255)
                            ->visible(fn (Get $get) => $get('status') === ProductStatus::Live->value),
                    ])
                    ->columns(2),

                Section::make('Images')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('logo')
                            ->collection('logo')
                            ->image()
                            ->imageEditor(),

                        SpatieMediaLibraryFileUpload::make('cover')
                            ->collection('cover')
                            ->image()
                            ->imageEditor()
                            ->helperText('Wide image for the product header.'),

                        SpatieMediaLibraryFileUpload::make('screenshots')
                            ->collection('screenshots')
                            ->image()
                            ->multiple()
                            ->reorderable()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
