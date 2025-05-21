<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CostController;
use App\Http\Controllers\DebtController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GeneralExpenseController;
use App\Http\Controllers\LocalityController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WaterConnectionController;
use App\Http\Controllers\AdvancePaymentController;
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

Route::get('/', function () {
    return view('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
});

Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

Route::group(['middleware' => ['auth']], function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('dashboard', DashboardController::class);

    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile/update', [ProfileController::class, 'profileUpdate'])->name('profile.update');
    Route::post('/profile/editImage', [ProfileController::class, 'updateImage'])->name('profile.update.image');
    Route::put('/profile/updatePassword', [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');

    Route::group(['middleware' => ['can:viewUser']], function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::resource('users', UserController::class);
        Route::post('/users/{user}/updateRole', [UserController::class, 'updateRole'])->name('users.updateRole');
        Route::put('/users/{user}/updatePassword', [UserController::class, 'updatePassword'])->name('users.updatePassword');
    });

    Route::group(['middleware' => ['can:viewCustomers']], function () {
        Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
        Route::resource('customers', CustomerController::class);
        Route::get('/customers-with-debts', [CustomerController::class, 'customersWithDebts'])->name('report.with-debts');
        Route::get('/report/pdfCustomers', [CustomerController::class, 'pdfCustomers'])->name('customers.pdfCustomers');
        Route::get('/report/current-customers', [CustomerController::class, 'reportCurrentCustomers'])->name('report.current-customers');
        Route::get('/payment-history/{id}', [CustomerController::class, 'generatePaymentHistoryReport'])->name('reports.paymentHistoryReport');
    });

    Route::group(['middleware' => ['can:viewRoles']], function () {
        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
        Route::resource('roles', RoleController::class);
    });

    Route::group(['middleware' => ['can:viewCost']], function () {
        Route:: resource('costs', CostController::class);
        Route::get('/costs', [CostController::class, 'index'])->name('costs.index');
    });

    Route::group(['middleware' => ['can:viewDebts']], function () {
        Route::post('/debts/assign-all', [DebtController::class, 'assignAll'])->name('debts.assignAll');
        Route::resource('debts', DebtController::class);
        Route::get('/getWaterConnections', [DebtController::class, 'getWaterConnections'])->name('getWaterConnections');
    });

    Route::group(['middleware' => ['can:viewPayments']], function () {
        Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::resource('payments', PaymentController::class);
        Route::get('/getWaterConnectionsByCustomer', [PaymentController::class, 'getWaterConnectionsByCustomer'])->name('getWaterConnectionsByCustomer');
        Route::get('/getDebtsByWaterConnection', [PaymentController::class, 'getDebtsByWaterConnection'])->name('getDebtsByWaterConnection');
        Route::get('/receipt-payment/{id}', [PaymentController::class, 'receiptPayment'])->name('reports.receiptPayment');
        Route::get('/client-payments', [PaymentController::class, 'clientPaymentReport'])->name('report.client-payments');
        Route::get('/weekly-earnings-report', [PaymentController::class, 'weeklyEarningsReport'])->name('report.weeklyEarningsReport');
        Route::get('/water-connection-payments', [PaymentController::class, 'waterConnectionPaymentsReport'])->name('report.waterConnectionPayments');
        Route::get('/annual-earnings-report/{year}', [PaymentController::class, 'annualEarningsReport']);
    });

    Route::group(['middleware' => ['can:viewLocality']], function () {
        Route::get('/localities', [LocalityController::class, 'index'])->name('localities.index');
        Route::resource('localities', LocalityController::class);
        Route::post('/localities/{locality}/update-logo', [LocalityController::class, 'updateLogo'])->name('localities.updateLogo');
        Route::get('/locality-earnings', [DashboardController::class, 'getEarningsByLocality'])->name('locality.earnings');
    });

    Route::group(['middleware' => ['can:viewWaterConnection']], function () {
        Route::get('/waterConnections', [WaterConnectionController::class, 'index'])->name('connections.index');
        Route::resource('waterConnections', WaterConnectionController::class);
    });

    Route::group(['middleware' => ['can:viewGeneralExpense']], function () {
        Route::get('/generalExpenses', [GeneralExpenseController::class, 'index'])->name('expenses.index');
        Route::resource('generalExpenses', GeneralExpenseController::class);
        Route::get('/weekly-expenses-report', [GeneralExpenseController::class, 'weeklyExpensesReport'])->name('report.weeklyExpensesReport');
        Route::get('/annual-expenses-report/{year}', [GeneralExpenseController::class, 'annualExpensesReport'])->name('report.annualExpensesReport');
        Route::get('/weekly-gains-report', [GeneralExpenseController::class, 'weeklyGainsReport'])->name('report.weeklyGainsReport');
        Route::get('/annual-gains-report/{year}', [GeneralExpenseController::class, 'annualGainsReport'])->name('report.annualGainsReport');
    });

    Route::get('/advancePayments', [AdvancePaymentController::class, 'index'])->name('advancePayments.index');
});
