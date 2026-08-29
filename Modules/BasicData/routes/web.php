<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - BasicData Module
|--------------------------------------------------------------------------
*/

Route::group(['middleware' => ['auth', 'permissionHandler']], function () {
    
    // Macro to register standard resource routes with export and bulk endpoints
    Route::macro('resourceWithExport', function ($uri, $controller) {
        Route::prefix($uri)
            ->name("{$uri}.")
            ->group(function () use ($controller) {
                Route::get('print', "{$controller}@print")->name('print');
                Route::get('csv', "{$controller}@csv")->name('csv');
                Route::get('excel', "{$controller}@excel")->name('excel');
                Route::get('pdf', "{$controller}@pdf")->name('pdf');
                Route::get('import', "{$controller}@import")->name('import');
                Route::get('importTemplate', "{$controller}@importTemplate")->name('importTemplate');
                Route::post('importSave', "{$controller}@importsave")->name('importSave');
                Route::post('bulk-delete', "{$controller}@bulkDelete")->name('bulkDelete');
                Route::post('bulk-status', "{$controller}@bulkStatus")->name('bulkStatus');
                Route::get('scopedaccess', "{$controller}@scopedaccess")->name('scopedaccess');
                Route::get('draft', "{$controller}@draft")->name('draft');
                Route::get('approve', "{$controller}@approve")->name('approve');
            });

        Route::resource($uri, $controller)->names($uri);
    });

    // BasicData Resources
    Route::resourceWithExport('products', 'DbProductController');
    Route::resourceWithExport('units', 'DbUnitController');
    Route::resourceWithExport('categories', 'DbCategoryController');
    Route::resourceWithExport('service_points', 'DbServicePointController');
    Route::resourceWithExport('kitchens', 'DbKitchenController');
});
