<?php

use Illuminate\Support\Facades\Route;
use App\Filament\Resources\MaintainResource;
use App\Http\Controllers\MaintainController;
use App\Models\Maintain;

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


Route::get('/maintain/{record}/print', function (Maintain $record) {
    // Load all relationships as defined in your model
    $record->load([
        'user', 
        'comments', 
        'fireDoorGuardChecks', 
        'fireDoorGuardBatteryReplacements'
    ]);
    // dd($record);
    return view('pdfs.maintain-html', [
        'record' => $record,
        'year' => request('year', date('Y')),
    ]);
})->name('maintain.print');