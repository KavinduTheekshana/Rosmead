<?php

namespace App\Filament\Resources\WindowCheckResource\Pages;

use App\Filament\Resources\WindowCheckResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWindowChecks extends ListRecords
{
    protected static string $resource = WindowCheckResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
