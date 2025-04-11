<?php

namespace App\Filament\Resources\MaintainResource\Pages;

use App\Filament\Resources\MaintainResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMaintains extends ListRecords
{
    protected static string $resource = MaintainResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
