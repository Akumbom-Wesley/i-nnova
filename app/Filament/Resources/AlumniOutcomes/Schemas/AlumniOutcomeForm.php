<?php

namespace App\Filament\Resources\AlumniOutcomes\Schemas;

use App\Filament\Support\LocaleTabs;
use App\Models\KickstarterTrack;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AlumniOutcomeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Person')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(120),

                        TextInput::make('organisation')
                            ->label('Now at')
                            ->maxLength(160),

                        SpatieMediaLibraryFileUpload::make('photo')
                            ->collection('photo')
                            ->image()
                            ->imageEditor()
                            ->avatar()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Outcome')
                    ->description('What the programme actually led to. This is the proof the Kickstarter page rests on.')
                    ->schema([
                        LocaleTabs::make(fn (string $locale) => [
                            TextInput::make("role.{$locale}")
                                ->label('Role')
                                ->maxLength(120),

                            Textarea::make("outcome.{$locale}")
                                ->label('Outcome')
                                ->rows(3)
                                ->columnSpanFull(),

                            Textarea::make("quote.{$locale}")
                                ->label('Quote')
                                ->rows(3)
                                ->columnSpanFull(),
                        ]),
                    ]),

                Section::make('Placement')
                    ->schema([
                        Select::make('kickstarter_track_id')
                            ->label('Track')
                            ->options(fn () => KickstarterTrack::query()->ordered()->get()->mapWithKeys(
                                fn (KickstarterTrack $track) => [$track->id => $track->name],
                            ))
                            ->searchable()
                            ->preload(),

                        TextInput::make('cohort_year')
                            ->label('Cohort year')
                            ->numeric()
                            ->minValue(2000)
                            ->maxValue(2100),

                        TextInput::make('sort_order')
                            ->label('Order')
                            ->numeric()
                            ->default(0),
                    ])
                    ->columns(3),
            ]);
    }
}
