<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth', 'permissionHandler']], function () {
    // تعريف macro مرة واحدة
    Route::macro('resourceWithExport', function ($uri, $controller) {
        Route::prefix($uri)
            ->name("{$uri}.")
            ->group(function () use ($controller) {
                Route::get('print', "{$controller}@print")->name('print');
                Route::get('csv', "{$controller}@csv")->name('csv');

                Route::get('excel', "{$controller}@excel")->name('excel');
                Route::get('pdf', "{$controller}@pdf")->name('pdf');
                Route::get('import', "{$controller}@import")->name('import');
                Route::get('children', "{$controller}@getChildren")->name('children');
                Route::post('importSave', "{$controller}@importsave")->name('importSave');
                Route::post('{id}/copy', "{$controller}@copy")->name('copy');
                Route::get('scopedaccess', "{$controller}@scopedaccess")->name('scopedaccess');
                Route::get('draft', "{$controller}@draft")->name('draft');
                Route::get('approve', "{$controller}@approve")->name('approve');
            });

        Route::resource($uri, $controller)->names($uri);
    });

    // الاستخدام
    Route::resourceWithExport('banks', 'BankController');
    Route::resourceWithExport('safes', 'SafeController');
    Route::resourceWithExport('bonds', 'FncBondController');


});
