<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\IncomeEntryController;
use App\Http\Controllers\ExpenseEntryController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\IncomeTypeController;
use App\Http\Controllers\ExpenseTypeController;
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

Route::middleware(['auth'])->group(function () {
    Route::get('/logout-auth', function() {
        Auth::logout();
        return redirect('/login');
    });
});

Route::get('/complete-registration', [RegisterController::class, 'completeRegistration'])->name('complete.registration');

// Route::middleware(['2fa', 'auth'])->group(function () {
Route::middleware(['auth'])->group(function () {
    Route::get('', [DashboardController::class, 'index'])->name('home');

    Route::post('/2fa', function () {
        return redirect(route('home'))->with('success', 'Login Success!');
    })->name('2fa');
});

// users url
Route::prefix('users')->as('users.')->middleware(['auth'])->group(function () {
    Route::get('/index', [UserController::class, 'index'])->middleware(['permission:view_users'])->name('user_index');
    Route::post('/datatable', [UserController::class, 'datatable'])->middleware(['permission:view_users'])->name('user_datatable');
    Route::post('/create-or-update/{user?}', [UserController::class, 'createOrUpdate'])->middleware(['permission:add_user'])->name('create_or_update');
    Route::get('/delete/{user?}', [UserController::class, 'delete'])->middleware(['permission:delete_user'])->name('user_delete');
    Route::post('/{user}/toggle-status', [UserController::class, 'toggleStatus'])->middleware(['permission:update_user'])->name('toggle_status');

    Route::get('/profile', [UserController::class, 'profile'])->middleware(['permission:view_profile'])->name('profile');
    Route::post('/update-profile/{user}', [UserController::class, 'updateProfile'])->middleware(['permission:update_profile'])->name('update_profile');
});

// permissions url
Route::prefix('permissions')->as('permissions.')->middleware(['auth'])->group(function () {
    Route::get('/index', [PermissionController::class, 'index'])->middleware(['permission:view_permissions'])->name('permissions_index');
    Route::get('/get-role-permissions', [PermissionController::class, 'rolePermission'])->middleware(['permission:view_permissions'])->name('get_role_permission');
    Route::post('/update-role-permissions', [PermissionController::class, 'assignPermissionsByRoles'])->middleware(['permission:view_permissions'])->name('update_role_permission');
});

// banks url
Route::prefix('banks')->as('banks.')->middleware(['auth'])->group(function () {
    Route::get('/index', [BankController::class, 'index'])->middleware(['permission:view_banks'])->name('index');
    Route::post('/datatable', [BankController::class, 'datatable'])->middleware(['permission:view_banks'])->name('datatable');
    Route::post('/create', [BankController::class, 'store'])->middleware(['permission:add_bank'])->name('create');
    Route::post('/update/{bank}', [BankController::class, 'update'])->middleware(['permission:edit_bank'])->name('update');
    Route::post('/{bank}/toggle-status', [BankController::class, 'toggleStatus'])->middleware(['permission:edit_bank'])->name('toggle_status');
    Route::get('/delete/{bank}', [BankController::class, 'destroy'])->middleware(['permission:delete_bank'])->name('delete');
});

// income-types url
Route::prefix('income-types')->as('income-types.')->middleware(['auth'])->group(function () {
    Route::get('/index', [IncomeTypeController::class, 'index'])->middleware(['permission:view_income_types'])->name('index');
    Route::post('/create', [IncomeTypeController::class, 'store'])->middleware(['permission:add_income_type'])->name('create');
    Route::post('/update/{incomeType}', [IncomeTypeController::class, 'update'])->middleware(['permission:edit_income_type'])->name('update');
    Route::post('/{incomeType}/toggle-status', [IncomeTypeController::class, 'toggleStatus'])->middleware(['permission:edit_income_type'])->name('toggle_status');
    Route::get('/delete/{incomeType}', [IncomeTypeController::class, 'destroy'])->middleware(['permission:delete_income_type'])->name('delete');
});

// expense-types url
Route::prefix('expense-types')->as('expense-types.')->middleware(['auth'])->group(function () {
    Route::get('/index', [ExpenseTypeController::class, 'index'])->middleware(['permission:view_expense_types'])->name('index');
    Route::post('/create', [ExpenseTypeController::class, 'store'])->middleware(['permission:add_expense_type'])->name('create');
    Route::post('/update/{expenseType}', [ExpenseTypeController::class, 'update'])->middleware(['permission:edit_expense_type'])->name('update');
    Route::post('/{expenseType}/toggle-status', [ExpenseTypeController::class, 'toggleStatus'])->middleware(['permission:edit_expense_type'])->name('toggle_status');
    Route::get('/delete/{expenseType}', [ExpenseTypeController::class, 'destroy'])->middleware(['permission:delete_expense_type'])->name('delete');
});

// Account routes
Route::prefix('accounts')->as('accounts.')->middleware(['auth'])->group(function () {
    Route::get('/index', [AccountController::class, 'index'])->name('index')->middleware('permission:view_accounts');
    Route::post('/store', [AccountController::class, 'store'])->name('store')->middleware('permission:add_account');
    Route::post('/update/{account}', [AccountController::class, 'update'])->name('update')->middleware('permission:edit_account');
    Route::delete('/delete/{account}', [AccountController::class, 'destroy'])->name('destroy')->middleware('permission:delete_account');
});

// Income entries routes
Route::prefix('income-entries')->as('income-entries.')->middleware(['auth'])->group(function () {
    Route::get('/index', [IncomeEntryController::class, 'index'])->name('index');
    Route::post('/create', [IncomeEntryController::class, 'store'])->name('create');
    Route::post('/update/{incomeEntry}', [IncomeEntryController::class, 'update'])->name('update');
    Route::get('/delete/{incomeEntry}', [IncomeEntryController::class, 'destroy'])->name('delete');
});

// Expense entries routes
Route::prefix('expense-entries')->as('expense-entries.')->middleware(['auth'])->group(function () {
    Route::get('/index', [ExpenseEntryController::class, 'index'])->name('index');
    Route::post('/create', [ExpenseEntryController::class, 'store'])->name('create');
    Route::post('/update/{expenseEntry}', [ExpenseEntryController::class, 'update'])->name('update');
    Route::get('/delete/{expenseEntry}', [ExpenseEntryController::class, 'destroy'])->name('delete');
});
