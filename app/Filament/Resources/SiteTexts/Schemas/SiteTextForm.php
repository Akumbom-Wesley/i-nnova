<?php

namespace App\Filament\Resources\SiteTexts\Schemas;

use App\Filament\Support\LocaleTabs;
use App\Models\SiteText;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SiteTextForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Where it appears')
                    ->schema([
                        TextInput::make('group')
                            ->label('Section')
                            ->disabled()
                            ->helperText('Set automatically from the template that uses this text.'),

                        Textarea::make('key')
                            ->label('Original wording')
                            ->rows(2)
                            ->disabled()
                            ->helperText('What this text said before anyone edited it. Kept so it can always be compared.'),
                    ])
                    ->columns(2),

                Section::make('The words')
                    ->description('Clear a field to fall back to the original wording rather than leaving the site blank.')
                    ->schema([
                        LocaleTabs::make(fn (string $locale) => [
                            Textarea::make("value.{$locale}")
                                ->label('Text')
                                ->rows(3)
                                ->columnSpanFull()
                                ->helperText(fn (?SiteText $record): string => filled($record?->defaultFor($locale))
                                    ? 'Originally: ' . $record->defaultFor($locale)
                                    : 'No original translation for this language.'),
                        ]),
                    ]),
            ]);
    }
}
