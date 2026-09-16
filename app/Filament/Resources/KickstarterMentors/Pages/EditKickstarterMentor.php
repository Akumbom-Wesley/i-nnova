<?php

namespace App\Filament\Resources\KickstarterMentors\Pages;

use App\Filament\Resources\KickstarterMentors\KickstarterMentorResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditKickstarterMentor extends EditRecord
{
    protected static string $resource = KickstarterMentorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
