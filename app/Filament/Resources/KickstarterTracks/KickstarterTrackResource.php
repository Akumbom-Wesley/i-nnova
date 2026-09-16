<?php

namespace App\Filament\Resources\KickstarterTracks;

use App\Filament\Resources\KickstarterTracks\Pages\CreateKickstarterTrack;
use App\Filament\Resources\KickstarterTracks\Pages\EditKickstarterTrack;
use App\Filament\Resources\KickstarterTracks\Pages\ListKickstarterTracks;
use App\Filament\Resources\KickstarterTracks\Schemas\KickstarterTrackForm;
use App\Filament\Resources\KickstarterTracks\Tables\KickstarterTracksTable;
use App\Models\KickstarterTrack;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class KickstarterTrackResource extends Resource
{
    protected static ?string $model = KickstarterTrack::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRocketLaunch;

    protected static string|UnitEnum|null $navigationGroup = 'Kickstarter';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'track';

    protected static ?string $recordTitleAttribute = 'slug';

    public static function form(Schema $schema): Schema
    {
        return KickstarterTrackForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KickstarterTracksTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListKickstarterTracks::route('/'),
            'create' => CreateKickstarterTrack::route('/create'),
            'edit' => EditKickstarterTrack::route('/{record}/edit'),
        ];
    }
}
