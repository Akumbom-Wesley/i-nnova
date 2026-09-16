<?php

namespace App\Filament\Resources\KickstarterTracks\Pages;

use App\Filament\Resources\KickstarterTracks\KickstarterTrackResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditKickstarterTrack extends EditRecord
{
    protected static string $resource = KickstarterTrackResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
