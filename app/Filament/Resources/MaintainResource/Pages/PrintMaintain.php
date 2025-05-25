<?php

namespace App\Filament\Resources\MaintainResource\Pages;

use App\Filament\Resources\MaintainResource;
use Filament\Resources\Pages\Page;

class PrintMaintain extends Page
{
    protected static string $resource = MaintainResource::class;

    protected static string $view = 'filament.resources.maintain-resource.pages.print-maintain';

    public function mount(): void
    {
        // Redirect to PDF response
        $pdfResponse = MaintainResource::printPdf($this->record);
        
        // If it's a PDF response, output it directly
        if ($pdfResponse instanceof \Illuminate\Http\Response) {
            $pdfResponse->send();
            exit;
        }
    }
}