<?php

use Illuminate\Support\Facades\Route;
use App\Filament\Resources\MaintainResource;
// Route::get('/', function () {
//     return view('welcome');
// });

Route::middleware(['auth'])->group(function () {
    Route::get('/maintain/{record}/print', function ($record) {
        return MaintainResource::printPdf($record);
    })->name('maintain.print');
    
    Route::get('/maintain/{record}/download', function ($record) {
        return MaintainResource::downloadPdf($record);
    })->name('maintain.download');
});