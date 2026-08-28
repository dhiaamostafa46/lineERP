<?php

use Illuminate\Support\Facades\Route;
use Modules\Pos\App\Http\Controllers\PosController;
use Modules\Pos\App\Http\Controllers\PosDeviceController;
use Modules\Pos\App\Http\Controllers\PosPaymentMethodController;
use Modules\Pos\App\Http\Controllers\PosReportController;
use Modules\Pos\App\Http\Controllers\Api\PosApiController;
use Modules\Pos\App\Http\Controllers\PosAuthController;

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
                Route::get('viewBranches', "{$controller}@viewBranches")->name('viewBranches');
                Route::get('scopedaccess', "{$controller}@scopedaccess")->name('scopedaccess');
                Route::post('{id}/copy', "{$controller}@copy")->name('copy');
                Route::get('draft', "{$controller}@draft")->name('draft');
                Route::get('approve', "{$controller}@approve")->name('approve');
            });

        Route::resource($uri, $controller)->names($uri);
    });

    // إضافة macro للتقارير لمحاكاة الفواتير
    Route::macro('resourceWithReports', function ($uri, $controller) {
        Route::prefix($uri)
            ->name("{$uri}.")
            ->group(function () use ($controller) {
                // تقارير الحركات
                Route::get('sales', "{$controller}@sales")->name('sales');
                Route::get('sessions', "{$controller}@sessions")->name('sessions');
                Route::get('category_sales', "{$controller}@categorySales")->name('category_sales');
                Route::get('product_sales', "{$controller}@productSales")->name('product_sales');
                Route::get('sessions_detailed', "{$controller}@sessionsDetailed")->name('sessions_detailed');
                
                // تقارير الأرباح
                Route::get('profit_sessions', "{$controller}@profitSessions")->name('profit_sessions');
                Route::get('profit_categories', "{$controller}@profitCategories")->name('profit_categories');
                Route::get('profit_products', "{$controller}@profitProducts")->name('profit_products');
                
                // تقارير أخرى
                Route::get('payment_methods', "{$controller}@paymentMethods")->name('payment_methods');
                Route::get('returns', "{$controller}@returns")->name('returns');
                Route::get('cash_movements', "{$controller}@cashMovements")->name('cash_movements');
                Route::get('taxes', "{$controller}@taxes")->name('taxes');
            });

        Route::resource($uri, $controller)->names($uri);
    });

    Route::get('print/{id}', [PosController::class, 'print'])->name('print');
    Route::get('session/print/{id}', [PosController::class, 'printSession'])->name('session.print');
    
    // POS Terminal selection page
    Route::get('select-device', [PosController::class, 'selectDevice'])->name('select_device');

    // Devices CRUD
    Route::resourceWithExport('devices', PosDeviceController::class);
    
    // Reports
    Route::resourceWithReports('reports', PosReportController::class);
    
    // Unified Shift Reports
    Route::get('reports/shift/{session_id}/z-report', [PosReportController::class, 'shiftZReport'])->name('reports.shift.z_report');
    Route::get('reports/shift/{session_id}/cash-ledger', [PosReportController::class, 'shiftCashLedger'])->name('reports.shift.cash_ledger');
    Route::get('reports/shift/{session_id}/detailed-sales', [PosReportController::class, 'shiftDetailedSales'])->name('reports.shift.detailed_sales');
    Route::get('reports/shift/{session_id}/journal-entries', [PosReportController::class, 'shiftJournalEntries'])->name('reports.shift.journal_entries');
    Route::get('reports/shift/{session_id}/sold-items', [PosReportController::class, 'shiftSoldItems'])->name('reports.shift.sold_items');
});

// POS Terminal Route - Independent from ERP auth
// Serves the Vue SPA for the cashier
Route::get('pos-terminal/{device:uuid}', [PosController::class, 'index'])->name('terminal');
