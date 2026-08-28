<?php

use Illuminate\Support\Facades\Route;
use Modules\BasicData\Http\Controllers\BasicDataController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::group([], function () {
//     Route::resource('basicdata', BasicDataController::class)->names('basicdata');
// });

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
                Route::get('importTemplate', "{$controller}@importTemplate")->name('importTemplate');
                Route::post('importSave', "{$controller}@importsave")->name('importSave');
                Route::post('{id}/copy', "{$controller}@copy")->name('copy');
                Route::get('scopedaccess', "{$controller}@scopedaccess")->name('scopedaccess');
                Route::get('draft', "{$controller}@draft")->name('draft');
                Route::get('approve', "{$controller}@approve")->name('approve');
            });

        Route::resource($uri, $controller)->names($uri);
    });

    // الاستخدام
    Route::resourceWithExport('products', 'DbProductController');
    Route::resourceWithExport('units', 'DbUnitController');
    Route::resourceWithExport('categories', 'DbCategoryController');
    Route::resourceWithExport('service_points', 'DbServicePointController');
    Route::resourceWithExport('kitchens', 'DbKitchenController');

  

    // Route::resource('products', 'DBProductController')->names('products');
    // Route::resource('units', 'DbUnitController')->names('units');
    // Route::resource('categories', 'DbCategoryController')->names('categories');
    // Route::resource('service_points', 'DbServicePointController')->names('service_points');
    // Route::resource('kitchens', 'DbKitchenController')->names('kitchens');
});
