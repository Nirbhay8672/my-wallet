<?php

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PermissionController;
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
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/logout-auth', [LoginController::class, 'logOut']);
});

Route::middleware(['2fa', 'auth'])->group(function () {
    Route::get('/home', [DashboardController::class, 'index'])->name('home');

    Route::post('/2fa', function () {
        return redirect(route('home'));
    })->name('2fa');
});

// users url
Route::prefix('users')->as('users.')->middleware(['auth', '2fa'])->group(function () {
    Route::get('/index', [UserController::class, 'index'])->middleware(['permission:view_users'])->name('user_index');
    Route::post('/datatable', [UserController::class, 'datatable'])->middleware(['permission:view_users'])->name('user_datatable');
    Route::post('/create-or-update/{user?}', [UserController::class, 'createOrUpdate'])->middleware(['permission:add_user'])
    ->name('create_or_update');
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

// projects url
Route::prefix('websites')->as('websites.')->middleware(['auth', '2fa','permission:view_websites'])->group(function () {
    Route::get('/index', [WebsiteController::class, 'index'])->name('websites_index');
    Route::post('/datatable', [WebsiteController::class, 'datatable'])->name('websites_datatable');
    Route::get('/details/{website}', [WebsiteController::class, 'details'])->name('websites_details');
});

// clients url
Route::prefix('clients')->as('clients.')->middleware(['auth', '2fa','permission:view_clients'])->group(function () {
    Route::get('/index', [ClientController::class, 'index'])->name('clients_index');
    Route::post('/datatable', [ClientController::class, 'datatable'])->name('clients_datatable');
    Route::post('/create-or-update/{client?}', [ClientController::class, 'createOrUpdate'])->name('create_or_update_client');
    Route::get('/delete/{client}', [ClientController::class, 'delete'])->name('delete_client');
    Route::post('/payments', [ClientController::class, 'payments'])->name('client_payments');
});

// payments url
Route::prefix('payments')->as('payments.')->middleware(['auth', '2fa', 'permission:view_payments'])->group(function () {
    Route::get('/index', [PaymentController::class, 'index'])->name('payments_index');
    Route::post('/datatable', [PaymentController::class, 'datatable'])->name('payments_datatable');
    Route::post('/create', [PaymentController::class, 'create'])->name('create_payment');
    Route::get('/generate-invoice/{payment}', [PaymentController::class, 'generateInvoice'])->name('generate-invoice');
});

// apis
Route::post('/add-website', [WebsiteController::class, 'addWebsite'])->name('add_website');
Route::post('/add-payment', [PaymentController::class, 'addPayment'])->name('add_payment');