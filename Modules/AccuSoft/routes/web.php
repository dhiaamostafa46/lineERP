<?php

use Illuminate\Support\Facades\Route;
use Modules\AccuSoft\App\Http\Controllers\AsFiscalYearController;
use Modules\AccuSoft\App\Http\Controllers\AsJournalEntryController;
use Modules\AccuSoft\App\Http\Controllers\AsSettingController;

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
//     Route::resource('accusoft', AccuSoftController::class)->names('accusoft');
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
                Route::get('downloadTemplate', "{$controller}@downloadTemplate")->name('downloadTemplate');
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
    Route::get('tree-accounts/get-next-code', 'AsTreeAccountController@getNextCode')->name('TreeAccounts.getNextCode');
    Route::get('cost-centers/get-next-code', 'AsCostCenterController@getNextCode')->name('CostCenter.getNextCode');
    Route::post('TreeAccounts/import-process', 'AsTreeAccountController@importProcess')->name('TreeAccounts.importProcess');
    Route::get('TreeAccounts/import-review', 'AsTreeAccountController@importReview')->name('TreeAccounts.importReview');
    Route::post('TreeAccounts/import-confirm', 'AsTreeAccountController@importConfirm')->name('TreeAccounts.importConfirm');
    Route::post('TreeAccounts/import-cancel', 'AsTreeAccountController@importCancel')->name('TreeAccounts.importCancel');

    Route::get('update-fixed-assets-type', function() {
        \App\Models\AccuSoft\TreeAccounts::where('code', 'like', '121%')->update(['account_type' => 14]);
        
        $account = \App\Models\AccuSoft\TreeAccounts::where('code', '42304')->first();
        if ($account) {
            \App\Models\AccuSoft\AccountMapping::updateOrCreate(
                ['mapping_key' => 'Expenseasste_depreciation'],
                [
                    'account_id' => $account->id,
                    'status' => \App\Models\AccuSoft\AccountMapping::STATUS_ACTIVE,
                    'ar' => ['name' => 'مصروف إهلاك الأصول الثابتة'],
                    'en' => ['name' => 'Expenseasste depreciation'],
                ]
            );
            return 'Fixed Assets account_type updated successfully to 14, and mapping for Expenseasste_depreciation added!';
        }
        
        return 'Fixed Assets updated to 14! (Note: Account 42304 not found, so mapping was not added).';
    });
    
    Route::resourceWithExport('TreeAccounts', 'AsTreeAccountController');
    Route::resourceWithExport('CostCenter', 'AsCostCenterController');
    Route::get('JournalEntry/pending', 'AsJournalEntryController@pending')->name('JournalEntry.pending');
    Route::post('JournalEntry/bulk-post', [AsJournalEntryController::class, 'bulkPost'])->name('JournalEntry.bulkPost');
   // Route::get('JournalEntry/delete-user-entries/{userId?}', [AsJournalEntryController::class, 'deleteUserEntries'])->name('JournalEntry.deleteUserEntries');
    Route::post('JournalEntry/{id}/post', [AsJournalEntryController::class, 'post'])->name('JournalEntry.post');
    Route::resourceWithExport('JournalEntry', 'AsJournalEntryController');
    Route::post('JournalEntry/{id}/verify-lock-password', [AsJournalEntryController::class, 'verifyLockPassword'])->name('JournalEntry.verifyLockPassword');
    Route::resourceWithExport('FiscalYear', 'AsFiscalYearController');
    Route::resourceWithExport('AccountingSettings', 'AsAccountingSettingsController');
    Route::resourceWithExport('AccountMapping', 'AsAccountMappingController');
    Route::get('Setting', [AsSettingController::class, 'index'])->name('Setting.index');
    Route::get('pdfdetails/{id}', [AsJournalEntryController::class, 'pdfdetails'])->name('JournalEntry.pdfdetails');
    Route::get('FiscalYear/{id}/close', [AsFiscalYearController::class, 'close'])->name('FiscalYear.close');
    Route::get('FiscalYear/{id}/reopen', [AsFiscalYearController::class, 'reopen'])->name('FiscalYear.reopen');

    // Assets
    Route::resource('depreciation_runs', 'DepreciationRunController')->only(['index', 'create', 'store']);
    Route::get('assets/unactivated', 'AssetController@unactivated')->name('assets.unactivated');
    Route::post('assets/{asset}/depreciate', 'AssetController@depreciate')->name('assets.depreciate');
    Route::post('assets/{asset}/dispose', 'AssetController@dispose')->name('assets.dispose');
    Route::post('assets/{asset}/depreciations/{depreciation}/execute', 'AssetController@executeDepreciation')->name('assets.execute_depreciation');
    Route::post('assets/forecast', 'AssetController@forecast')->name('assets.forecast');
   
    Route::resourceWithExport('assets', 'AssetController');
    Route::resourceWithExport('assetcategories', 'AssetCategoryController');

    // =========================================================================================================
    // =========================================================================================================
    // =========================================================================================================
    // =========================================================================================================
    // =========================================================================================================
    // =========================================================================================================
    Route::macro('resourceWithReports', function ($uri, $controller) {
        Route::prefix($uri)
            ->name("{$uri}.")
            ->group(function () use ($controller) {
                Route::get('incomeStatement', "{$controller}@incomeStatement")->name('incomeStatement');
                Route::get('balanceSheet', "{$controller}@balanceSheet")->name('balanceSheet');
                Route::get('trialBalance', "{$controller}@trialBalance")->name('trialBalance');
                Route::get('generalLedger', "{$controller}@generalLedger")->name('generalLedger');
                Route::get('cashFlow', "{$controller}@cashFlow")->name('cashFlow');
                Route::get('accountstatement', "{$controller}@accountstatement")->name('accountstatement');
                Route::get('costcenter', "{$controller}@costcenter")->name('costcenter');
                Route::get('assets', "{$controller}@assets")->name('assets');
                Route::get('journalEntries', "{$controller}@journalEntries")->name('journalEntries');
            });

        Route::resource($uri, $controller)->names($uri);
    });

    Route::resourceWithReports('reports', 'AsReportController');
});
