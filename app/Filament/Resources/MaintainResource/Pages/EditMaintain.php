<?php

namespace App\Filament\Resources\MaintainResource\Pages;

use App\Filament\Resources\MaintainResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMaintain extends EditRecord
{
    protected static string $resource = MaintainResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
