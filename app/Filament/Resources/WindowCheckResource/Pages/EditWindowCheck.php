<?php

namespace App\Filament\Resources\WindowCheckResource\Pages;

use App\Filament\Resources\WindowCheckResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWindowCheck extends EditRecord
{
    protected static string $resource = WindowCheckResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
