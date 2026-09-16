<?php

namespace App\Filament\Resources\KickstarterMentors\Pages;

use App\Filament\Resources\KickstarterMentors\KickstarterMentorResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListKickstarterMentors extends ListRecords
{
    protected static string $resource = KickstarterMentorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
