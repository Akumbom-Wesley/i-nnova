<?php

namespace App\Filament\Resources\GalleryImages\Schemas;

use App\Enums\GalleryPlacement;
use App\Filament\Support\LocaleTabs;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class GalleryImageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('The photograph or video')
                    ->description('Upload the real photograph here. Anything uploaded replaces the stand-in below automatically. A video can be uploaded or linked, and still wants a photograph as its still.')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('image')
                            ->collection('image')
                            ->image()
                            ->imageEditor()
                            ->columnSpanFull(),

                        SpatieMediaLibraryFileUpload::make('video')
                            ->collection('video')
                            ->acceptedFileTypes(['video/mp4', 'video/webm', 'video/quicktime'])
                            ->maxSize(102400)
                            ->helperText('Optional. Footage you took yourself, up to 100 MB. The photograph above is used as the still shown before it plays.')
                            ->columnSpanFull(),

                        TextInput::make('video_url')
                            ->label('Or a link to YouTube or Vimeo')
                            ->url()
                            ->maxLength(500)
                            ->helperText('Used instead of an uploaded file. Paste the ordinary watch link; it is turned into an embed.')
                            ->columnSpanFull(),

                        TextInput::make('external_url')
                            ->label('Stand-in image address')
                            ->url()
                            ->maxLength(500)
                            ->helperText('A temporary image from the web, used only until a real photograph is uploaded. Clear it once you have.')
                            ->columnSpanFull()
                            ->disabled(fn (Get $get): bool => filled($get('image')))
                            ->dehydrated(),
                    ]),

                Section::make('Placement')
                    ->schema([
                        Select::make('placement')
                            ->options(GalleryPlacement::class)
                            ->default(GalleryPlacement::About)
                            ->required(),

                        TextInput::make('sort_order')
                            ->label('Order')
                            ->numeric()
                            ->default(0),

                        Toggle::make('is_active')
                            ->label('Show on the site')
                            ->default(true),

                        Toggle::make('is_featured')
                            ->label('Featured')
                            ->helperText('Featured photographs are the few shown on the page itself. Everything else waits in the full gallery. Mark none and the first three in this order are used.')
                            ->default(false),
                    ])
                    ->columns(2),

                Section::make('Words')
                    ->schema([
                        LocaleTabs::make(fn (string $locale) => [
                            TextInput::make("title.{$locale}")
                                ->label('Title')
                                ->maxLength(120),

                            TextInput::make("alt.{$locale}")
                                ->label('Alt text')
                                ->maxLength(180)
                                ->helperText('What the photograph shows, for anyone who cannot see it. Falls back to the title if left empty.'),

                            Textarea::make("caption.{$locale}")
                                ->label('Caption')
                                ->rows(2)
                                ->columnSpanFull(),
                        ]),
                    ]),
            ]);
    }
}
