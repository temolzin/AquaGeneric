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

Route::get('/annual-earnings-report/{year}', [PaymentController::class, 'annualEarningsReport']);
Route::get('customers/pdfCustomers', [CustomerController::class, 'pdfCustomers'])->name('customers.pdfCustomers');
Route::get('/report/current-customers', [CustomerController::class, 'reportCurrentCustomers'])->name('report.current-customers');
Route::get('/customers-with-debts', [CustomerController::class, 'customersWithDebts'])->name('report.with-debts');


Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');


Route::group(['middleware' => ['auth']], function () {

    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::resource('users', UserController::class);

    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::resource('customers', CustomerController::class);
    
    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::resource('roles', RoleController::class);
 
    Route::resource('costs', CostController::class);
    Route::get('/costs', [CostController::class, 'index'])->name('costs.index');

    Route::post('/debts/assign-all', [DebtController::class, 'assignAll'])->name('debts.assignAll');
    Route::resource('debts', DebtController::class);

    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::resource('payments', PaymentController::class);

    Route::get('/getCustomerDebts', [PaymentController::class, 'getCustomerDebts'])->name('getCustomerDebts');
    
    Route::post('/users/{user}/updateRole', [UserController::class, 'updateRole'])->name('users.updateRole');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('dashboard', DashboardController::class);

    Route::get('/receipt-payment/{id}', [PaymentController::class, 'receiptPayment'])->name('reports.receiptPayment');
});
