<?php

namespace App\Filament\Resources\SiteTexts\Pages;

use App\Filament\Resources\SiteTexts\SiteTextResource;
use Filament\Resources\Pages\ListRecords;

class ListSiteTexts extends ListRecords
{
    protected static string $resource = SiteTextResource::class;

    /**
     * Nothing to create: the list comes from the templates.
     */
    protected function getHeaderActions(): array
    {
        return [];
    }
}
