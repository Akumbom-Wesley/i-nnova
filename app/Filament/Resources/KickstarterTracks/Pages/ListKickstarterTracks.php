<?php

namespace App\Filament\Resources\KickstarterTracks\Pages;

use App\Filament\Resources\KickstarterTracks\KickstarterTrackResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListKickstarterTracks extends ListRecords
{
    protected static string $resource = KickstarterTrackResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
