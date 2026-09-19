<?php

namespace App\Filament\Resources\Clients\Schemas;

use App\Filament\Support\LocaleTabs;
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

/**
 * A client and, if you fill in the story, its page under /work.
 *
 * The written sections are optional on purpose. A client with only a name and
 * a logo is a logo on the wall; fill in the summary and it earns a page.
 */
class ClientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('The institution')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(160)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                if (blank($state) || filled($get('slug'))) {
                                    return;
                                }

                                $set('slug', Str::slug($state));
                            }),

                        TextInput::make('slug')
                            ->required()
                            ->maxLength(180)
                            ->unique(ignoreRecord: true)
                            ->helperText('The address of its page, if it has one.'),

                        TextInput::make('website_url')
                            ->label('Website')
                            ->url()
                            ->maxLength(255),

                        Select::make('sector_id')
                            ->label('Sector')
                            ->relationship('sector', 'name')
                            ->searchable()
                            ->preload(),

                        // Many to many, because an institution can run more
                        // than one of ours and often does.
                        Select::make('products')
                            ->label('Products they run')
                            ->relationship('products', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Logos and photographs')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('logo')
                            ->collection('logo')
                            ->image(),

                        SpatieMediaLibraryFileUpload::make('cover')
                            ->collection('cover')
                            ->image()
                            ->helperText('The banner on their page.'),

                        SpatieMediaLibraryFileUpload::make('images')
                            ->collection('images')
                            ->image()
                            ->multiple()
                            ->reorderable()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('The story')
                    ->description('Optional. Fill in the summary and this client gets a page of its own under Our work. Leave it empty and they stay a logo on the home page.')
                    ->schema([
                        LocaleTabs::make(fn (string $locale) => [
                            Textarea::make("summary.{$locale}")
                                ->label('Summary')
                                ->rows(2)
                                ->columnSpanFull()
                                ->helperText('One or two sentences. Writing this is what gives them a page.'),

                            Textarea::make("challenge.{$locale}")
                                ->label('The challenge')
                                ->rows(4),

                            Textarea::make("solution.{$locale}")
                                ->label('What we built')
                                ->rows(4),

                            Textarea::make("results.{$locale}")
                                ->label('The result')
                                ->rows(4)
                                ->columnSpanFull(),

                            Textarea::make("quote.{$locale}")
                                ->label('Quote')
                                ->rows(3)
                                ->columnSpanFull(),

                            TextInput::make("quote_role.{$locale}")
                                ->label('Their role'),
                        ], columns: 2),

                        TextInput::make('quote_attribution')
                            ->label('Quoted person')
                            ->maxLength(160)
                            ->helperText('Not translated. A person is called the same thing in both languages.'),
                    ]),

                Section::make('Placement')
                    ->schema([
                        Toggle::make('is_verified')
                            ->label('Verified relationship')
                            ->helperText('Only verified clients are ever rendered. Leave this off until the relationship is confirmed.'),

                        Toggle::make('is_featured')
                            ->label('Lead the home page')
                            ->helperText('Featured clients fill the deployments band on the home page.'),

                        TextInput::make('sort_order')
                            ->label('Order')
                            ->numeric()
                            ->default(0)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
