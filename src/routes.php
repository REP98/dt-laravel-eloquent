<?php
use Illuminate\Support\Facades\Route;
use DTLaravelEloquent\Controllers\RDataTableController;

Route::prefix('dt')->middleware(['web'])->group(function(){
    Route::post('/export/excel/', [RDataTableController::class, 'exportExcel'])->name('dtexport.excel');
    Route::post('/export/pdf/', [RDataTableController::class, 'exportPdf'])->name('dtexport.pdf');
});
