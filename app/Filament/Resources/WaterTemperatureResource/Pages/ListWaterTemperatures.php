<?php

namespace App\Filament\Resources\WaterTemperatureResource\Pages;

use App\Filament\Resources\WaterTemperatureResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWaterTemperatures extends ListRecords
{
    protected static string $resource = WaterTemperatureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
