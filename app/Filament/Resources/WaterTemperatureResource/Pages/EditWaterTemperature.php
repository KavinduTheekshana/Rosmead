<?php

namespace App\Filament\Resources\WaterTemperatureResource\Pages;

use App\Filament\Resources\WaterTemperatureResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWaterTemperature extends EditRecord
{
    protected static string $resource = WaterTemperatureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
