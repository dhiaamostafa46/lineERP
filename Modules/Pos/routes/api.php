<?php

use Illuminate\Support\Facades\Route;
use Modules\Pos\Http\Controllers\PosController;

/*
 *--------------------------------------------------------------------------
 * API Routes
 *--------------------------------------------------------------------------
 *
 * Here is where you can register API routes for your application. These
 * routes are loaded by the RouteServiceProvider within a group which
 * is assigned the "api" middleware group. Enjoy building your API!
 *
*/

use Modules\Pos\App\Http\Controllers\Api\PosApiController;
use Modules\Pos\App\Http\Controllers\PosAuthController;
use Modules\Pos\App\Http\Controllers\Api\PosBootstrapController;

Route::group(['prefix' => 'pos', 'as' => 'api.pos.'], function () {
    // Public routes (requires device UUID in header usually, but no sanctum token yet)
    Route::post('login', [PosAuthController::class, 'apiLogin'])
        ->middleware('throttle:6,1')
        ->name('login');
    
    // Protected routes (requires Sanctum Bearer Token and Device Verification)
    Route::group(['middleware' => ['auth:sanctum', 'verify.pos.device']], function () {
        Route::post('logout', [PosAuthController::class, 'apiLogout'])->name('logout');
        
        // Single Entry Point for POS App
        Route::get('bootstrap', [PosBootstrapController::class, 'index'])->name('bootstrap');
        
        // Products & Checkout
        Route::get('products', [PosApiController::class, 'products'])->name('products');
        Route::post('checkout', [PosApiController::class, 'checkout'])->name('checkout');

        // Session Management
        Route::get('session/invoices', [PosApiController::class, 'sessionInvoices'])->name('session.invoices');
        Route::get('invoice/{id}', [PosApiController::class, 'getInvoiceForReturn'])->name('invoice.return_details');
        Route::get('session/status', [PosApiController::class, 'sessionStatus'])->name('session.status');
        Route::get('session/ping', [PosApiController::class, 'ping'])->name('session.ping');
        Route::post('session/open', [PosApiController::class, 'openSession'])->name('session.open');
        Route::post('session/close', [PosApiController::class, 'closeSession'])->name('session.close');
        Route::post('session/transaction', [PosApiController::class, 'sessionTransaction'])->name('session.transaction');
        
        // Customers
        Route::post('customer', [PosApiController::class, 'createCustomer'])->name('customer.create');
    });
});
