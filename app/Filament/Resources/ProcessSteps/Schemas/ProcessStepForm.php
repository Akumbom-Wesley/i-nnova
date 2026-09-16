<?php

namespace App\Filament\Resources\ProcessSteps\Schemas;

use App\Filament\Support\LocaleTabs;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ProcessStepForm
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

                    Textarea::make("summary.{$locale}")
                        ->label('Summary')
                        ->rows(3)
                        ->helperText('One or two lines. This is what the About page shows.')
                        ->columnSpanFull(),

                    RichEditor::make("body.{$locale}")
                        ->label('Detail')
                        ->columnSpanFull(),
                ]),

                TextInput::make('sort_order')
                    ->label('Order')
                    ->numeric()
                    ->default(0)
                    ->helperText('Steps read in this order.'),
            ]);
    }
}
