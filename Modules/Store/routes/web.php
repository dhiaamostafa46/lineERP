<?php

use Illuminate\Support\Facades\Route;
use Modules\Store\App\Http\Controllers\StSettingController;

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
                Route::post('importSave', "{$controller}@importsave")->name('importSave');
                Route::post('{id}/copy', "{$controller}@copy")->name('copy');
                Route::get('scopedaccess', "{$controller}@scopedaccess")->name('scopedaccess');
                Route::get('draft', "{$controller}@draft")->name('draft');
                Route::get('approve', "{$controller}@approve")->name('approve');
            });

        Route::resource($uri, $controller)->names($uri);
    });

    Route::resourceWithExport('stores', 'StStoreController');
    Route::resourceWithExport('openingbalance', 'StOpeningBalanceController');
    Route::resourceWithExport('damaged', 'StDamagedController');
    Route::prefix('reservation')->name('reservation.')->group(function () {
        Route::post('{id}/authorize', 'StReservationController@authorizeReservation')->name('authorize');
        Route::post('{id}/return', 'StReservationController@returnToWarehouse')->name('return');
    });
    Route::resourceWithExport('reservation', 'StReservationController');
    // Route::resourceWithExport('pending', 'StPendingController');
    Route::resourceWithExport('receiving', 'StReceivingController');
    Route::resourceWithExport('issuing', 'StIssuingController');
    Route::prefix('direct_transfer')->name('direct_transfer.')->group(function () {
        Route::get('{id}/validate', 'StDirectTransferController@validateTransfer')->name('validate');
        Route::post('{id}/validate', 'StDirectTransferController@storeValidation')->name('store_validation');
        Route::get('{id}/return', 'StDirectTransferController@returnTransfer')->name('return');
        Route::post('{id}/return', 'StDirectTransferController@storeReturn')->name('store_return');
    });
    Route::resourceWithExport('direct_transfer', 'StDirectTransferController');
    //   Route::resourceWithExport('inventory_orders', 'StInventoryOrderController');
    Route::prefix('settlement')->name('settlement.')->group(function () {
        Route::get('import', 'StSettlementController@import')->name('import');
        Route::post('process-smart-import', 'StSettlementController@processSmartImport')->name('process_smart_import');
        Route::post('{id}/authorize', 'StSettlementController@authorizeSettlement')->name('authorize');
    });
    Route::resourceWithExport('settlement', 'StSettlementController');

    Route::resource('setting', 'StSettingController')->names('setting');

    Route::get('getproducts', [StSettingController::class, 'getProduct'])->name('Ajex.getproducts');
    Route::get('inventory-settings', [StSettingController::class, 'inventorysettings'])->name('Ajex.inventorysettings');

    // Route::resource('products', 'DBProductController')->names('products');
    // Route::resource('units', 'DbUnitController')->names('units');
    // Route::resource('categories', 'DbCategoryController')->names('categories');
    // Route::resource('service_points', 'DbServicePointController')->names('service_points');
    // Route::resource('kitchens', 'DbKitchenController')->names('kitchens');

    Route::macro('resourceWithReports', function ($uri, $controller) {
        Route::prefix($uri)
            ->name("{$uri}.")
            ->group(function () use ($controller) {
                Route::get('stockMovement', "{$controller}@stockMovement")->name('stockMovement');
                Route::get('stockBalance', "{$controller}@stockBalance")->name('stockBalance');
                Route::get('inventoryValuation', "{$controller}@inventoryValuation")->name('inventoryValuation');
                Route::get('lowStock', "{$controller}@lowStock")->name('lowStock');
                Route::get('inventoryCount', "{$controller}@inventoryCount")->name('inventoryCount');
                Route::get('pendingStock', "{$controller}@pendingStock")->name('pendingStock');
            });

        Route::resource($uri, $controller)->names($uri);
    });

    Route::resourceWithReports('reports', 'StReportController');
});
