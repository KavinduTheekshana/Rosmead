<?php
// App\Filament\Resources\MaintainResource\Pages\ViewMaintain.php
namespace App\Filament\Resources\MaintainResource\Pages;

use App\Filament\Resources\MaintainResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewMaintain extends ViewRecord
{
    protected static string $resource = MaintainResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}