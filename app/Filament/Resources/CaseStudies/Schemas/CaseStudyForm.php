<?php

namespace App\Filament\Resources\CaseStudies\Schemas;

use App\Filament\Support\LocaleTabs;
use App\Models\Product;
use App\Models\Sector;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CaseStudyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Institution')
                    ->schema([
                        TextInput::make('institution')
                            ->required()
                            ->maxLength(160)
                            ->helperText('Not translated. PAXHI is PAXHI in both languages.')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                if (blank($state) || filled($get('slug'))) {
                                    return;
                                }

                                $set('slug', Str::slug($state));
                            }),

                        TextInput::make('slug')
                            ->required()
                            ->maxLength(160)
                            ->unique(ignoreRecord: true),

                        Select::make('sector_id')
                            ->label('Sector')
                            ->options(fn () => Sector::query()->ordered()->get()->mapWithKeys(
                                fn (Sector $sector) => [$sector->id => $sector->name],
                            ))
                            ->searchable()
                            ->preload(),

                        Select::make('product_id')
                            ->label('Product deployed')
                            ->options(fn () => Product::query()->ordered()->get()->mapWithKeys(
                                fn (Product $product) => [$product->id => $product->name],
                            ))
                            ->searchable()
                            ->preload(),
                    ])
                    ->columns(2),

                Section::make('The story')
                    ->description('Challenge, solution, results. This is the section that closes.')
                    ->schema([
                        LocaleTabs::make(fn (string $locale) => [
                            Textarea::make("summary.{$locale}")
                                ->label('Summary')
                                ->rows(3)
                                ->helperText('One paragraph, used on the Work index card.')
                                ->columnSpanFull(),

                            RichEditor::make("challenge.{$locale}")
                                ->label('Challenge')
                                ->columnSpanFull(),

                            RichEditor::make("solution.{$locale}")
                                ->label('Solution')
                                ->columnSpanFull(),

                            RichEditor::make("results.{$locale}")
                                ->label('Results')
                                ->columnSpanFull(),
                        ]),
                    ]),

                Section::make('Quote')
                    ->schema([
                        LocaleTabs::make(fn (string $locale) => [
                            Textarea::make("quote.{$locale}")
                                ->label('Quote')
                                ->rows(3)
                                ->columnSpanFull(),

                            TextInput::make("quote_role.{$locale}")
                                ->label('Their role')
                                ->maxLength(120),
                        ]),

                        TextInput::make('quote_attribution')
                            ->label('Who said it')
                            ->maxLength(120),
                    ]),

                Section::make('Images')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('logo')
                            ->collection('logo')
                            ->image()
                            ->helperText('The institution logo.'),

                        SpatieMediaLibraryFileUpload::make('cover')
                            ->collection('cover')
                            ->image()
                            ->imageEditor(),

                        SpatieMediaLibraryFileUpload::make('images')
                            ->collection('images')
                            ->image()
                            ->multiple()
                            ->reorderable()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Placement')
                    ->schema([
                        TextInput::make('sort_order')
                            ->label('Order')
                            ->numeric()
                            ->default(0),

                        Toggle::make('is_featured')
                            ->label('Feature on the home page'),
                    ])
                    ->columns(2),
            ]);
    }
}
