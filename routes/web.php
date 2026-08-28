<?php

use App\Http\Controllers\AreasController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BranchesController;
use App\Http\Controllers\CitiesController;
use App\Http\Controllers\CompaniesController;
use App\Http\Controllers\CompanyContractsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DatabaseAuditController;
use App\Http\Controllers\DeviceSessionController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\Hub\ApplicationController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\LookupController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\TaxAccountController;
use App\Http\Controllers\TemplatesController;
use App\Http\Controllers\UserController;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('generator_builder', '\InfyOm\GeneratorBuilder\Controllers\GeneratorBuilderController@builder')->name('io_generator_builder');

Route::get('field_template', '\InfyOm\GeneratorBuilder\Controllers\GeneratorBuilderController@fieldTemplate')->name('io_field_template');

Route::get('relation_field_template', '\InfyOm\GeneratorBuilder\Controllers\GeneratorBuilderController@relationFieldTemplate')->name('io_relation_field_template');

Route::post('generator_builder/generate', '\InfyOm\GeneratorBuilder\Controllers\GeneratorBuilderController@generate')->name('io_generator_builder_generate');

Route::post('generator_builder/rollback', '\InfyOm\GeneratorBuilder\Controllers\GeneratorBuilderController@rollback')->name('io_generator_builder_rollback');

Route::post('generator_builder/generate-from-file', '\InfyOm\GeneratorBuilder\Controllers\GeneratorBuilderController@generateFromFile')->name('io_generator_builder_generate_from_file');

// update permission
Route::get('/permissions/update', function () {
    return Artisan::call('permissions:update');
});

// Maintenance Tool: Sync Chart of Accounts with Customers and Suppliers
Route::get('/maintenance/sync-accounts', [\App\Http\Controllers\MaintenanceController::class, 'syncAccounts'])->name('maintenance.sync-accounts')->middleware('auth');

Route::get('/removeAllprogect', function () {
    $dateLimit = '2026-06-20';
    $products = \App\Models\BasicDataApp\Product::where('created_at', '<', $dateLimit)->get();

    $deletedCount = 0;

    // جميع الجداول التي تعبر عن "عمليات" (فواتير، عروض سعر، حركات مخزنية، إلخ)
    $operationTables = [
        'sales_invoice_items',
        'purchase_invoice_items',
        'quotation_items',
        'purchase_order_items',
        'st_damaged_items',
        'st_direct_transfer_items',
        'st_issuing_items',
        'st_opening_balance_items',
        'st_receiving_items',
        'st_reservation_items',
        'st_settlement_items',
        'stock_movements',
    ];

    foreach ($products as $product) {
        $isUsed = false;

        foreach ($operationTables as $table) {
            if (\Illuminate\Support\Facades\Schema::hasTable($table)) {
                if (\Illuminate\Support\Facades\DB::table($table)->where('product_id', $product->id)->exists()) {
                    $isUsed = true;
                    break;
                }
            }
        }

        if (! $isUsed) {
            $product->forceDelete();
            $deletedCount++;
        }
    }

    return "تم حذف $deletedCount منتج (لم يتم استخدامهم في أي عملية) قبل تاريخ $dateLimit بنجاح.";
});

// Route::get('/seed-settings', function () {
//     Artisan::call('db:seed', [
//         '--class' => 'SettingTableSeeder'
//     ]);

//     return "Settings Seeder executed successfully";
// });
Route::get('/migrateDatabase', function () {
    Artisan::call('migrate');

    return '<pre>'.Artisan::output().'</pre>';
});

// Route::get('/check-fixes', [DatabaseAuditController::class, 'checkFixes']);
// Route::get('/apply-fixes', [DatabaseAuditController::class, 'applyFixes']);

Route::get('/RecordEmployeePresence', function () {
    $startOfMonth = \Carbon\Carbon::now()->startOfMonth();
    $today = \Carbon\Carbon::now()->endOfMonth();
    $startOfMonth = Carbon::parse('2026-03-1')->startOfDay();
    $today = Carbon::parse('2026-05-31')->startOfDay();
    $results = [];

    // حلقة لمعالجة كل يوم من بداية الشهر إلى اليوم
    for ($date = $startOfMonth->copy(); $date->lte($today); $date->addDay()) {
        $dateString = $date->toDateString();

        try {
            Artisan::call('attendance:record', [
                '--date' => $dateString,
            ]);

            $results[] = "✅ تم معالجة: {$dateString}";
        } catch (\Exception $e) {
            $results[] = "❌ خطأ في: {$dateString} - ".$e->getMessage();
        }
    }

    return response()->json([
        'message' => 'تم الانتهاء من معالجة الحضور',
        'from' => $startOfMonth->toDateString(),
        'to' => $today->toDateString(),
        'total_days' => count($results),
        'details' => $results,
    ]);
});

// switch language
Route::get('language/switch/{locale}', [LanguageController::class, 'switchLang'])->name('switchLang');

Route::view('/privacy', 'pages.privacy')->name('privacy');

// //Mobile App login
// Route::middleware('auth:sanctum')->get('/mobile-login', function (\Illuminate\Http\Request  $request) {

//     $user = $request->user(); // من Sanctum token

//     if (!$user) {
//         abort(401);
//     }

//     Auth::guard('web')->login($user);

//      //return redirect()->route('dashboard');
//   return response()->json([
//         'url' =>route('dashboard')
//     ]);
// });
// /////////////////////////////////////
Route::middleware('auth:sanctum')->get('/get-dashboard-url', function (\Illuminate\Http\Request $request) {

    $user = $request->user();

    // / إنشاء nonce عشوائي
    $nonce = \Str::random(40);

    // نخزن nonce لمدة دقيقة
    Cache::put('login_nonce_'.$nonce, $user->id, now()->addMinute());

    // إنشاء رابط مؤقت مع nonce
    $url = URL::temporarySignedRoute(
        'mobile.login',
        now()->addMinute(),
        ['nonce' => $nonce]
    );

    return response()->json([
        'dashboard_url' => $url,
    ]);
});
Route::get('/mobile-login', function (\Illuminate\Http\Request $request) {

    // التحقق من التوقيع
    if (! $request->hasValidSignature()) {
        abort(403, 'Invalid signature');
        // return "❌ signature failed";
    }

    $nonce = $request->nonce;

    $userId = Cache::pull('login_nonce_'.$nonce); // pull = يقرأ ويحذف

    if (! $userId) {
        abort(403, 'Expired or already used');
        // return "❌ nonce not found or used";
    }

    $user = \App\Models\User::findOrFail($userId);

    Auth::guard('web')->login($user);

    return redirect()->route('dashboard');

})->name('mobile.login');
// ////////////////////////////////////////////////
Route::group(['middleware' => ['guest']], function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate'])->name('authenticate');
    Route::get('/Register', [AuthController::class, 'Register'])->name('auth.Register');
    Route::post('/Register', [AuthController::class, 'AuthRegister'])->name('auth.AuthRegister');

    Route::view('reset-password', 'auth.resetpassword')->name('reset.password');

    Route::get('check-device', [AuthController::class, 'checkdevice'])->name('auth.check_device');
    // Route::get('password/reset', 'ResetPasswordController@showLinkRequestForm')->name('password.request');
    // Route::post('password/email', 'ResetPasswordController@sendResetLinkEmail')->name('password.email');
    // Route::get('password/reset/{token}', 'ResetPasswordController@showResetForm')->name('password.reset');
    // Route::post('password/reset', 'ResetPasswordController@reset')->name('password.update');

    Route::get('password/reset', [ResetPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('password/OPT', [ResetPasswordController::class, 'sendResetLinkOTP'])->name('password.OPT');
    Route::post('password/OPTCheck', [ResetPasswordController::class, 'OPTCheck'])->name('password.OPTCheck');
    Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

});

Route::group(['middleware' => ['auth', 'permissionHandler']], function () {
    Route::get('logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/', [DashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('UserActivity', [DashboardController::class, 'UserActivity'])->name('UserActivity');

    Route::get('Lookup/getproducts', [LookupController::class, 'getproducts'])->name('Lookup.getproducts');
    Route::get('Lookup/TreeAccounts', [LookupController::class, 'getTreeAccounts'])->name('Lookup.TreeAccounts');
    Route::get('Lookup/customers', [LookupController::class, 'getCustomers'])->name('Lookup.getCustomers');
    Route::get('Lookup/suppliers', [LookupController::class, 'getSuppliers'])->name('Lookup.getSuppliers');
    Route::get('Lookup/stores', [LookupController::class, 'getStores'])->name('Lookup.getStores');
    Route::get('Lookup/users', [LookupController::class, 'getUsers'])->name('Lookup.getUsers');
    // Route::post('notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
    // Route::post('notifications/clear-read', [NotificationController::class, 'clearRead'])->name('notifications.clearRead');
    // Route::get('notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    //   Route::get('notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');

    // ====================== password
    Route::get('resetuserpass/{id}', [AuthController::class, 'resetpassword'])->name('users.resetuserpass');
    Route::view('reset-changePassword', 'auth.ChangePassword')->name('reset.changePassword');
    Route::post('password/changePassword', [AuthController::class, 'changePassword'])->name('password.changePassword');

    // ====================== payment
    Route::get('/payment/{PaymentType}/{type}', [App\Http\Controllers\SubscriptionController::class, 'payment'])->name('Subscription.payment');
    Route::get('/paymentSubscription/{id}', [App\Http\Controllers\SubscriptionController::class, 'paymentSubscription'])->name('Subscription.paymentSubscription');
    Route::GET('/paymentSubscriptionSave/{id}', [App\Http\Controllers\SubscriptionController::class, 'paymentSubscriptionSave'])->name('Subscription.paymentSubscriptionSave');
    Route::get('/paymentMessage/{id}', [App\Http\Controllers\SubscriptionController::class, 'paymentMessage'])->name('Subscription.paymentMessage');

    // Route::resource('themes', App\Http\Controllers\ThemeController::class);
    // Route::resource('languages', App\Http\Controllers\LanguageController::class);

    /*
|--------------------------------------------------------------------------
| Custom Macro: resourceWithExport
|--------------------------------------------------------------------------
*/
    Route::macro('resourceWithExport', function (string $uri, string $controller) {
        Route::prefix($uri)
            ->name("{$uri}.")
            ->group(function () use ($controller) {
                Route::get('print', [$controller, 'print'])->name('print');
                Route::get('csv', [$controller, 'csv'])->name('csv');
                Route::get('excel', [$controller, 'excel'])->name('excel');

                Route::get('pdf', [$controller, 'pdf'])->name('pdf');
                Route::get('import', [$controller, 'import'])->name('import');
                Route::post('import-save', [$controller, 'importSave'])->name('importSave');
                Route::post('{id}/copy', [$controller, 'copy'])->name('copy');
                Route::get('draft', [$controller, 'draft'])->name('draft');
                Route::get('approve', [$controller, 'approve'])->name('approve');
            });

        Route::resource($uri, $controller)->names($uri);
    });

    /*
|--------------------------------------------------------------------------
|  Resource Routes
|--------------------------------------------------------------------------
*/
    // الاستخدام
    Route::resourceWithExport('taxaccounts', TaxAccountController::class);
    Route::resourceWithExport('users', UserController::class);
    Route::resourceWithExport('roles', RoleController::class);
    Route::resourceWithExport('employees', EmployeeController::class);
    Route::resourceWithExport('Branches', BranchesController::class);
    Route::resourceWithExport('Templates', TemplatesController::class);
    Route::resource('Areas', AreasController::class)->names('Areas');
    Route::resource('Cities', CitiesController::class)->names('Cities');
    Route::resource('Companies', CompaniesController::class)->names('Companies');
    Route::resource('CompanyContracts', CompanyContractsController::class)->names('CompanyContracts');
    Route::resourceWithExport('Organization', OrganizationController::class);
    Route::resourceWithExport('subscriptions', SubscriptionController::class);
    Route::resourceWithExport('DeviceSessions', DeviceSessionController::class);
    Route::get('notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
    Route::get('notifications/{id}/read', [NotificationController::class, 'read'])->name('notifications.read');
    Route::get('notifications/{id}/confirm', [NotificationController::class, 'confirm'])->name('notifications.confirm');
    Route::resourceWithExport('notifications', NotificationController::class);
    Route::resource('settings', SettingController::class)->only(['edit', 'update']);

    // ====================== Applications & Evix Hub Integrations ======================
    Route::get('applications', [ApplicationController::class, 'index'])->name('applications.index');
    Route::post('applications/sync-hub', [ApplicationController::class, 'syncFromHub'])->name('applications.sync_hub');
    Route::get('applications/{code}/details', [ApplicationController::class, 'details'])->name('applications.details');
    Route::get('applications/{code}', [ApplicationController::class, 'show'])->name('applications.show');
    Route::post('applications/{code}/activate', [ApplicationController::class, 'activate'])->name('applications.activate');
    Route::post('applications/{code}/deactivate', [ApplicationController::class, 'deactivate'])->name('applications.deactivate');
    Route::post('applications/{code}/toggle-status', [ApplicationController::class, 'toggleStatus'])->name('applications.toggle_status');

    // Global Route for viewBranches
    Route::get('global/branches', [\App\Http\Controllers\BranchesController::class, 'viewBranches'])->name('global.viewBranches');
});

Route::get('download/app', function () {
    $path = storage_path('app/public/appdownload/v1.0.0.apk');

    if (! file_exists($path)) {
        abort(404);
    }

    return response()->download($path);
});

Route::get('hrapp/download', function () {
    return view('get_hr_app');
});
