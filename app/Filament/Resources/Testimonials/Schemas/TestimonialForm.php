<?php

namespace App\Filament\Resources\Testimonials\Schemas;

use App\Filament\Support\LocaleTabs;
use App\Models\Product;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TestimonialForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Quote')
                    ->schema([
                        LocaleTabs::make(fn (string $locale) => [
                            Textarea::make("quote.{$locale}")
                                ->label('Quote')
                                ->required(LocaleTabs::isDefault($locale))
                                ->rows(4)
                                ->columnSpanFull(),

                            TextInput::make("person_role.{$locale}")
                                ->label('Role')
                                ->maxLength(120),
                        ]),
                    ]),

                Section::make('Attribution')
                    ->description('A quote carries more weight with a face and an organisation behind it. Both are worth filling in.')
                    ->schema([
                        TextInput::make('person_name')
                            ->label('Name')
                            ->required()
                            ->maxLength(120),

                        TextInput::make('organisation')
                            ->maxLength(160),

                        SpatieMediaLibraryFileUpload::make('photo')
                            ->collection('photo')
                            ->image()
                            ->imageEditor()
                            ->avatar()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Placement')
                    ->schema([
                        Select::make('product_id')
                            ->label('About which product')
                            ->options(fn () => Product::query()->ordered()->get()->mapWithKeys(
                                fn (Product $product) => [$product->id => $product->name],
                            ))
                            ->searchable()
                            ->preload(),

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
