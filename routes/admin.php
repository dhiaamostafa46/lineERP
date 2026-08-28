<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminPanel\RoleController;
use App\Http\Controllers\AdminPanel\UserController;
use App\Http\Controllers\AdminPanel\AdminController;
use App\Http\Controllers\AdminPanel\SettingController;
use App\Http\Controllers\AdminPanel\DashboardController;
use Illuminate\Support\Facades\Artisan;

// update permission
Route::get('/permissions/update', function () {
    return Artisan::call('permissions:update');
});

Route::group(['middleware' => ['guest']], function () {
    Route::get('/login', 'AuthController@login')->name('login');
    Route::post('/login', 'AuthController@authenticate')->name('authenticate');
});

Route::group(['middleware' => ['auth:admin', 'permissionHandler']], function () {
    Route::get('logout', 'AuthController@logout')->name('logout');
    Route::get('/', [DashboardController::class, 'dashboard'])->name('dashboard');

    Route::resource('users', UserController::class);
    Route::get('users/deactivate/{id}', [UserController::class, 'deactivate'])->name('users.deactivate');
    Route::get('users/activate/{id}', [UserController::class, 'activate'])->name('users.activate');

    Route::resource('admins', AdminController::class);
    Route::resource('roles', RoleController::class);


    Route::resource('settings', SettingController::class)->only(['edit', 'update']);
});
