<?php

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\SourceController;
use App\Http\Controllers\WebsiteController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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

Auth::routes();

Route::get('auth/google', [LoginController::class, 'redirectToGoogle']);
Route::get('auth/google/callback', [LoginController::class, 'handleGoogleCallback']);

Route::get('/complete-registration', [RegisterController::class, 'completeRegistration'])->name('complete.registration');

Route::middleware(['auth'])->group(function () {
    Route::get('/logout-auth', [LoginController::class, 'logOut']);
});

Route::middleware(['2fa', 'auth'])->group(function () {
    Route::get('', [DashboardController::class, 'index'])->name('home');

    Route::post('/2fa', function () {
        return redirect(route('home'))->with('success', 'Login Success!');
    })->name('2fa');
});

// users url
Route::prefix('users')->as('users.')->middleware(['auth', '2fa'])->group(function () {
    Route::get('/index', [UserController::class, 'index'])->middleware(['permission:view_users'])->name('user_index');
    Route::post('/datatable', [UserController::class, 'datatable'])->middleware(['permission:view_users'])->name('user_datatable');
    Route::post('/create-or-update/{user?}', [UserController::class, 'createOrUpdate'])->middleware(['permission:add_user'])->name('create_or_update');
    Route::get('/delete/{user?}', [UserController::class, 'delete'])->middleware(['permission:delete_user'])->name('user_delete');

    Route::get('/profile', [UserController::class, 'profile'])->middleware(['permission:view_profile'])->name('profile');
    Route::post('/update-profile/{user}', [UserController::class, 'updateProfile'])->middleware(['permission:update_profile'])->name('update_profile');
});

// permissions url
Route::prefix('permissions')->as('permissions.')->middleware(['auth', '2fa'])->group(function () {
    Route::get('/index', [PermissionController::class, 'index'])->middleware(['permission:view_permissions'])->name('permissions_index');
    Route::get('/get-role-permissions', [PermissionController::class, 'rolePermission'])->middleware(['permission:view_permissions'])->name('get_role_permission');
    Route::post('/update-role-permissions', [PermissionController::class, 'assignPermissionsByRoles'])->middleware(['permission:view_permissions'])->name('update_role_permission');
});

// sources url
Route::prefix('sources')->as('sources.')->middleware(['auth', '2fa'])->group(function () {
    Route::get('/index', [SourceController::class, 'index'])->middleware(['permission:view_sources'])->name('source_index');
    Route::post('/datatable', [UserController::class, 'datatable'])->middleware(['permission:view_sources'])->name('source_datatable');
    Route::post('/create-or-update/{user?}', [UserController::class, 'createOrUpdate'])->middleware(['permission:add_source'])->name('create_or_update');
    Route::get('/delete/{user?}', [UserController::class, 'delete'])->middleware(['permission:delete_source'])->name('source_delete');
});
