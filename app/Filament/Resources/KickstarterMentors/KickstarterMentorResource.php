<?php

namespace App\Filament\Resources\KickstarterMentors;

use App\Filament\Resources\KickstarterMentors\Pages\CreateKickstarterMentor;
use App\Filament\Resources\KickstarterMentors\Pages\EditKickstarterMentor;
use App\Filament\Resources\KickstarterMentors\Pages\ListKickstarterMentors;
use App\Filament\Resources\KickstarterMentors\Schemas\KickstarterMentorForm;
use App\Filament\Resources\KickstarterMentors\Tables\KickstarterMentorsTable;
use App\Models\KickstarterMentor;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class KickstarterMentorResource extends Resource
{
    protected static ?string $model = KickstarterMentor::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;

    protected static string|UnitEnum|null $navigationGroup = 'Kickstarter';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'mentor';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return KickstarterMentorForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KickstarterMentorsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListKickstarterMentors::route('/'),
            'create' => CreateKickstarterMentor::route('/create'),
            'edit' => EditKickstarterMentor::route('/{record}/edit'),
        ];
    }
}
