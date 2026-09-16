<?php

namespace App\Filament\Resources\Leads\Pages;

use App\Filament\Resources\Leads\LeadResource;
use App\Models\Lead;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditLead extends EditRecord
{
    protected static string $resource = LeadResource::class;

    /**
     * Opening a lead is what marks it read, so the sidebar badge tracks what
     * someone has actually looked at rather than what they remembered to tick.
     */
    protected function afterFill(): void
    {
        /** @var Lead $lead */
        $lead = $this->getRecord();

        $lead->markAsRead();
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
