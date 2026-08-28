<?php

use Illuminate\Support\Facades\Route;
use Modules\Invoices\App\Http\Controllers\InvSettingController;
use Modules\Invoices\App\Http\Controllers\PurchaseInvoiceController;
use Modules\Invoices\App\Http\Controllers\PurchaseReturnInvoiceController;
use Modules\Invoices\App\Http\Controllers\InvoiceSerialController;

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
                Route::get('importTemplate', "{$controller}@importTemplate")->name('importTemplate');
                Route::get('children', "{$controller}@getChildren")->name('children');
                Route::get('scopedaccess', "{$controller}@scopedaccess")->name('scopedaccess');
                Route::post('importSave', "{$controller}@importsave")->name('importSave');
                Route::post('process-smart-import', "{$controller}@processSmartImport")->name('process_smart_import');
                Route::post('analyze-with-ai', "{$controller}@analyzeWithAI")->name('analyze_with_ai');
                Route::post('{id}/copy', "{$controller}@copy")->name('copy');
                Route::get('draft', "{$controller}@draft")->name('draft');
                Route::get('approve', "{$controller}@approve")->name('approve');
            });

        Route::resource($uri, $controller)->names($uri);
    });

    // إضافة macro للتقارير لمحاكاة المخزون (متوافق مع الأنظمة السعودية)
    Route::macro('resourceWithReports', function ($uri, $controller) {
        Route::prefix($uri)
            ->name("{$uri}.")
            ->group(function () use ($controller) {
                // تقارير الفواتير الأساسية
                Route::get('sales', "{$controller}@salesInvoices")->name('sales');
                Route::get('purchases', "{$controller}@purchaseInvoices")->name('purchases');
                // تقارير المديونية والربحية
                Route::get('customer-aging', "{$controller}@customerAging")->name('customer_aging');
                Route::get('supplier-aging', "{$controller}@supplierAging")->name('supplier_aging');
                Route::get('profit', "{$controller}@productProfit")->name('profit');
                Route::get('daily', "{$controller}@dailySummary")->name('daily');

                // تقارير الزكاة والضريبة (Saudi Specific)
                Route::get('tax', "{$controller}@taxReport")->name('tax');
            });

        Route::resource($uri, $controller)->names($uri);
    });

    // الاستخدام بنفس طريقة المخزون
    Route::resourceWithReports('reports', 'InvReportController');

    Route::get('Setting/zatca', [InvSettingController::class, 'zatca'])->name('Setting.zatca');
    Route::post('Setting/zatcaStore/{id}', [InvSettingController::class, 'zatcaStore'])->name('Setting.zatcaStore');
    Route::post('Setting/zatca/production/{id}', [InvSettingController::class, 'requestProduction'])->name('Setting.zatca.production');
    Route::resourceWithExport('Setting', 'InvSettingController');
    Route::resourceWithExport('customers', 'InvCustomerController');
    Route::resourceWithExport('suppliers', 'InvSupplierController');
    Route::get('purchase/recalculate-all', [PurchaseInvoiceController::class, 'recalculateAll'])->name('purchase.recalculate_all');
    Route::resourceWithExport('purchase', 'PurchaseInvoiceController');
    Route::get('purchase_orders/{id}/convert', 'PurchaseOrderController@convertToInvoice')->name('purchase_orders.convert');
    Route::resourceWithExport('purchase_orders', 'PurchaseOrderController');
    Route::resourceWithExport('purchase_return', 'PurchaseReturnInvoiceController');
    Route::resourceWithExport('sales', 'SalesInvoiceController');
    Route::resourceWithExport('sales_return', 'SalesReturnInvoiceController');
    Route::resourceWithExport('sales_debit', 'SalesDebitNoteController');
    // Quotations
    Route::get('quotations/{id}/convert', 'QuotationController@convertToInvoice')->name('quotations.convert');
    Route::patch('quotations/{id}/status', 'QuotationController@updateStatus')->name('quotations.status');
    Route::resourceWithExport('quotations', 'QuotationController');
});

// معالجة serial وإعادة احتساب الفواتير القديمة - بدون middleware مقيد
Route::prefix('invoices-serials')->name('invoices.serials.')->group(function () {
    Route::get('status', [InvoiceSerialController::class, 'getStatus'])->name('status');
    Route::match(['get', 'post'], 'generate', [InvoiceSerialController::class, 'generateSerials'])->name('generate');
    Route::get('recalculate-all', [PurchaseInvoiceController::class, 'recalculateAll'])->name('recalculate_all');
});

