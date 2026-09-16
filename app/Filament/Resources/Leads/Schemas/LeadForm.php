<?php

namespace App\Filament\Resources\Leads\Schemas;

use App\Enums\LeadStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LeadForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('What they sent')
                    ->description('Submitted through the public contact form. Nothing here is editable.')
                    ->schema([
                        TextInput::make('name')->disabled(),
                        TextInput::make('email')->disabled(),
                        TextInput::make('phone')->placeholder('Not given')->disabled(),
                        TextInput::make('organisation')->placeholder('Not given')->disabled(),
                        TextInput::make('subject')->placeholder('None')->disabled()->columnSpanFull(),
                        Textarea::make('message')->rows(8)->disabled()->columnSpanFull(),
                        TextInput::make('locale')
                            ->label('Language used')
                            ->disabled()
                            ->helperText('Reply in the language they wrote in.'),
                    ])
                    ->columns(2),

                Section::make('Handling')
                    ->schema([
                        Select::make('status')
                            ->options(LeadStatus::class)
                            ->required(),
                    ]),
            ]);
    }
}
