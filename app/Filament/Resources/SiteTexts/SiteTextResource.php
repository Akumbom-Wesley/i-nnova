<?php

namespace App\Filament\Resources\SiteTexts;

use App\Filament\Resources\SiteTexts\Pages\EditSiteText;
use App\Filament\Resources\SiteTexts\Pages\ListSiteTexts;
use App\Filament\Resources\SiteTexts\Schemas\SiteTextForm;
use App\Filament\Resources\SiteTexts\Tables\SiteTextsTable;
use App\Models\SiteText;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

/**
 * Every fixed string on the site: headings, eyebrows, leads, button labels.
 */
class SiteTextResource extends Resource
{
    protected static ?string $model = SiteText::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLanguage;

    protected static string|UnitEnum|null $navigationGroup = 'Configuration';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'wording';

    protected static ?string $pluralModelLabel = 'wording';

    protected static ?string $recordTitleAttribute = 'key';

    /**
     * Rows come from scanning the templates, not from anyone typing them, so
     * there is no create page. `php artisan site:sync-texts` adds new ones.
     */
    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return SiteTextForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SiteTextsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSiteTexts::route('/'),
            'edit' => EditSiteText::route('/{record}/edit'),
        ];
    }
}
