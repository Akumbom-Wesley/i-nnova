<?php

namespace App\Filament\Resources\CompanyValues;

use App\Filament\Resources\CompanyValues\Pages\CreateCompanyValue;
use App\Filament\Resources\CompanyValues\Pages\EditCompanyValue;
use App\Filament\Resources\CompanyValues\Pages\ListCompanyValues;
use App\Filament\Resources\CompanyValues\Schemas\CompanyValueForm;
use App\Filament\Resources\CompanyValues\Tables\CompanyValuesTable;
use App\Models\CompanyValue;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class CompanyValueResource extends Resource
{
    protected static ?string $model = CompanyValue::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSparkles;

    protected static string|UnitEnum|null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 4;

    // The model is CompanyValue only because "values" is reserved in MySQL.
    // Editors should just see "Values".
    protected static ?string $modelLabel = 'value';

    protected static ?string $pluralModelLabel = 'values';

    public static function form(Schema $schema): Schema
    {
        return CompanyValueForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CompanyValuesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCompanyValues::route('/'),
            'create' => CreateCompanyValue::route('/create'),
            'edit' => EditCompanyValue::route('/{record}/edit'),
        ];
    }
}
