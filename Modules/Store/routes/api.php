<?php

use Illuminate\Support\Facades\Route;
use Modules\Store\App\Http\Controllers\StSettingController;

Route::prefix('v1')->group(function () {

    Route::get('test', function () {
        return ['test' => 'working from module'];
    })->name("API.load");


    //   Route::get('products/{product}/units', [StSettingController::class, 'getProductUnits'])->name('products.units');

      Route::get('products/getData', [StSettingController::class, 'getProduct'])->name('products.getData');



    //   Route::get('products/{product}/units-sizes', [StSettingController::class, 'getProductsizes'])->name('products.sizes');


});
