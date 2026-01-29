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
use App\Http\Controllers\FaultReportController;
use App\Http\Controllers\LocalityController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WaterConnectionController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\AdvancePaymentController;
use App\Http\Controllers\IncidentCategoriesController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LogIncidentController;
use App\Http\Controllers\IncidentStatusController;
use App\Http\Controllers\MailConfigurationController;
use App\Http\Controllers\ExpiredSubscriptionController;
use App\Http\Controllers\LocalityNoticeController;
use App\Http\Controllers\ReportListController;
use App\Http\Controllers\TokenController;
use App\Http\Middleware\CheckSubscription;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\ExpenseTypeController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\MovementHistoryController;
use App\Http\Controllers\InventoryCategoryController;
use App\Http\Controllers\CustomerFaultReportController;
use App\Http\Controllers\EarningTypeController;
use App\Http\Controllers\GeneralEarningController;

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

Route::view('/', 'home')->name('home');
Route::view('/login', 'login')->name('login');

Route::get('/dashboard', function () {
    return view('dashboard');
});

Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/toma/{hash}', [WaterConnectionController::class, 'showPublic'])->name('waterConnections.public');

Route::group(['middleware' => ['auth', CheckSubscription::class]], function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('dashboard', DashboardController::class);
    Route::post('/dashboard/report/sendEmailsForDebtsExpiringSoon', [DashboardController::class, 'sendEmailsForDebtsExpiringSoon'])->name('dashboard.sendEmailsForDebtsExpiringSoon');

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
        Route::get('/report/pdfCustomersSummary', [CustomerController::class, 'generateCustomerSummaryPdf'])->name('customers.pdfCustomersSummary');
        Route::get('/report/current-customers', [CustomerController::class, 'reportCurrentCustomers'])->name('report.current-customers');
        Route::get('/payment-history/{id}', [CustomerController::class, 'generatePaymentHistoryReport'])->name('reports.paymentHistoryReport');
        Route::get('/generate-user-access-pdf/{hash}', [CustomerController::class, 'generateUserAccessPDF'])->name('generate.user.access.pdf');
        Route::post('/customers/{id}/update-password', [CustomerController::class, 'updatePassword'])->name('customers.updatePassword');
        Route::post('/customers/{id}/assign-password', [CustomerController::class, 'assignOrUpdatePassword'])->name('customers.assignPassword');
        Route::post('/customers/import', [CustomerController::class, 'import'])->name('customers.import');
        Route::get('/customers/download-template', [CustomerController::class, 'downloadTemplate'])->name('customers.downloadTemplate');
    });

    Route::group(['middleware' => ['can:viewRoles']], function () {
        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
        Route::resource('roles', RoleController::class);
    });

    Route::group(['middleware' => ['can:viewCost']], function () {
        Route::resource('costs', CostController::class);
        Route::get('/costs', [CostController::class, 'index'])->name('costs.index');
        Route::get('/reports/generateCostListReport', [CostController::class, 'generateCostListReport'])->name('report.generateCostListReport');
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
        Route::get('/cash-closures-report', [PaymentController::class, 'cashClosurePaymentsReport'])->name('cash-closures.report');
    });

    Route::group(['middleware' => ['can:viewLocality']], function () {
        Route::get('/localities', [LocalityController::class, 'index'])->name('localities.index');
        Route::resource('localities', LocalityController::class);
        Route::post('/localities/{locality}/update-logo', [LocalityController::class, 'updateLogo'])->name('localities.updateLogo');
        Route::get('/locality-earnings', [DashboardController::class, 'getEarningsByLocality'])->name('locality.earnings');
        Route::put('/localities/{locality}/mailConfiguration',[MailConfigurationController::class, 'createOrUpdateMailConfigurations'])->name('mailConfigurations.createOrUpdate');
        Route::post('/localities/generateTeoken', [LocalityController::class, 'generateToken'])->name('localities.generateToken');
        Route::post('/localities/{locality}/update-pdf-background', [LocalityController::class, 'updatePdfBackground'])->name('localities.updatePdfBackground');
        Route::get('/reports/movements/generate', [MovementHistoryController::class, 'generatePDF'])->name('reports.generatePdfMovementsHistory');
    });

    Route::group(['middleware' => ['can:viewWaterConnection']], function () {
        Route::get('/waterConnections', [WaterConnectionController::class, 'index'])->name('connections.index');
        Route::resource('waterConnections', WaterConnectionController::class);
        Route::patch('/waterConnections/{id}/cancel', [WaterConnectionController::class, 'cancel'])->name('waterConnections.cancel');
        Route::patch('/waterConnections/{id}/reactivate', [WaterConnectionController::class, 'reactivate'])->name('waterConnections.reactivate');
        Route::get('/waterConnections/{id}/qr-generate', [WaterConnectionController::class, 'generateQrAjax'])->name('waterConnections.qr-generate');
        Route::get('/waterConnections/{id}/qr-download', [WaterConnectionController::class, 'downloadQr'])->name('waterConnections.qr-download');
    });

    Route::group(['middleware' => ['can:viewInventory']], function () {
        Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
        Route::resource('inventory', InventoryController::class);
        Route::get('/reports/pdfInventory', [InventoryController::class, 'generateInventoryPdf'])->name('inventory.pdfInventory');
        Route::post('/inventory/update-amount', [InventoryController::class, 'updateAmount'])->name('inventory.updateAmount');
    });

    Route::group(['middleware' => ['can:viewInventoryCategories']], function () {
        Route::get('/inventoryCategories', [InventoryCategoryController::class, 'index'])->name('inventoryCategories.index');
        Route::resource('inventoryCategories', InventoryCategoryController::class);
    });

    Route::group(['middleware' => ['can:viewGeneralExpense']], function () {
        Route::get('/generalExpenses', [GeneralExpenseController::class, 'index'])->name('expenses.index');
        Route::resource('generalExpenses', GeneralExpenseController::class);
        Route::get('/weekly-expenses-report', [GeneralExpenseController::class, 'weeklyExpensesReport'])->name('report.weeklyExpensesReport');
        Route::get('/annual-expenses-report/{year}', [GeneralExpenseController::class, 'annualExpensesReport'])->name('report.annualExpensesReport');
        Route::get('/weekly-gains-report', [GeneralExpenseController::class, 'weeklyGainsReport'])->name('report.weeklyGainsReport');
        Route::get('/annual-gains-report/{year}', [GeneralExpenseController::class, 'annualGainsReport'])->name('report.annualGainsReport');
    });

    Route::group(['middleware' => ['can:viewAdvancePayments']], function () {
        Route::get('/advancePayments', [AdvancePaymentController::class, 'index'])->name('advancePayments.index');
        Route::get('/getAdavancedPaymentReportWithConnection', [AdvancePaymentController::class, 'generateAdvancedPaymentReport'])->name('advancePayments.report');
        Route::post('/advancePaymentsGraphReport', [AdvancePaymentController::class, 'generatePaymentGraphReport'])->name('report.advancePaymentGraphReport');
        Route::get('/getCustomersWithAdvancePayments', [AdvancePaymentController::class, 'getCustomersWithAdvancePayments'])->name('getCustomersWithAdvancePayments');
        Route::get('/getAdvanceDebtDates', [AdvancePaymentController::class, 'getAdvanceDebtDates'])->name('getAdvanceDebtDates');
        Route::get('/advancePaymentsHistoryReport', [AdvancePaymentController::class, 'generateAdvancedPaymentHistoryReport'])->name('advancePayments.historyReport');
    });

    Route::group(['middleware' => ['can:viewIncidentCategories']], function () {
        Route::resource('incidentCategories', IncidentCategoriesController::class);
        Route::get('/reports/generateIncidentCategoyListReport', [IncidentCategoriesController::class, 'generateIncidentCategoyListReport'])->name('report.generateIncidentCategoyListReport');
    });

    Route::group(['middleware' => ['can:viewIncidents']], function () {
        Route::resource('incidents', IncidentController::class);
        Route::post('/logIncidents', [LogIncidentController::class, 'store'])->name('logsIncidents.store');
        Route::get('/reports/generateIncidentListReport', [IncidentController::class, 'generateIncidentListReport'])->name('report.generateIncidentListReport');
        Route::post('/incidents/update-status', [IncidentController::class, 'updateStatus'])->name('incidents.updateStatus');
    });

    Route::group(['middleware' => ['can:viewEmployee']], function () {
        Route::resource('employees', EmployeeController::class);
        Route::get('/reports/generateEmployeeListReport', [EmployeeController::class, 'generateEmployeeListReport'])->name('report.generateEmployeeListReport');
        Route::post('/employees/import', [EmployeeController::class, 'import'])->name('employees.import');
    });

    Route::group(['middleware' => ['can:viewIncidentStatuses']], function () {
        Route::resource('incidentStatuses', IncidentStatusController::class);
        Route::get('/reports/generateIncidentStatusListReport', [IncidentStatusController::class, 'generateIncidentStatusListReport'])->name('report.generateIncidentStatusListReport');
    });

    Route::group(['middleware' => ['can:viewFaultReport']], function () {
        Route::get('/faultReport', [FaultReportController::class, 'index'])->name('faultReport.index');
        Route::resource('faultReport', FaultReportController::class);
    });

    Route::group(['middleware' => ['can:viewCustomerFaultReports']], function () {
        Route::get('/viewMyFaultReports', [CustomerFaultReportController::class, 'index'])->name('customerFaultReports.index');
        Route::resource('customerFaultReports', CustomerFaultReportController::class);
    });

    Route::group(['middleware' => ['can:viewNotice']], function () {
        Route::get('/localityNotices', [LocalityNoticeController::class, 'index'])->name('localityNotices.index');
        Route::resource('localityNotices', LocalityNoticeController::class);
        Route::post('/localityNotices/{id}/toggle-status', [LocalityNoticeController::class, 'toggleStatus'])->name('localityNotices.toggle-status');
        Route::get('/api/localities/{localityId}/active-notices', [LocalityNoticeController::class, 'getActiveByLocality'])->name('localityNotices.active-by-locality');
        Route::get('localityNotices/{id}/download', [LocalityNoticeController::class, 'downloadAttachment'])->name('localityNotices.download');
    });

    Route::group(['middleware' => ['can:viewCustomerPayments']], function () {
        Route::get('/viewCustomerPayments', [PaymentController::class, 'showCustomerPayments'])->name('viewCustomerPayments.index');
        Route::get('/receipt/{paymentId}', [PaymentController::class, 'receiptPayment'])->name('viewCustomerPayments.receipt');
    });

    Route::group(['middleware' => ['can:viewWaterConnections']], function () {
        Route::get('/viewCustomerWaterConnections', [WaterConnectionController::class, 'showCustomerWaterConnections'])->name('viewCustomerWaterConnections.index');
    });

    Route::group(['middleware'=> ['can:viewCustomerDebts']], function() {
        Route::get('/viewCustomerDebts', [DebtController::class, 'showCustomerDebts'])->name('viewCustomerDebts.index');
    });

    Route::group(['middleware' => ['can:viewCustomerNotices']], function (){
    Route::get('customer/notices/{id}/file', [LocalityNoticeController::class, 'downloadAttachment'])->name('customer.notices.file');
    });

    Route::group(['middleware' => ['can:viewReportsLists']], function () {
        Route::get('/reportList', [ReportListController::class, 'index'])->name('reportList.index');
    });

    Route::group(['middleware' => ['can:viewMemberships']], function () {
        Route::resource('memberships', MembershipController::class);
    });

    Route::group(['middleware' => ['can:viewExpenseTypes']], function () {
        Route::get('/expenseTypes', [ExpenseTypeController::class, 'index'])->name('expenseTypes.index');
        Route::resource('expenseTypes', ExpenseTypeController::class);
    });

    Route::group(['middleware' => ['can:viewSections']], function () {
        Route::get('/sections', [SectionController::class, 'index'])->name('sections.index');
        Route::resource('sections', SectionController::class);
        Route::get('/reports/pdfSections', [SectionController::class, 'pdfSections'])->name('reports.pdfSections');
    });

    Route::group(['middleware' => ['can:viewCustomerPayments']], function () {
        Route::get('/viewCustomerPayments', [PaymentController::class, 'showCustomerPayments'])->name('viewCustomerPayments.index');
        Route::get('/receipt/{paymentId}', [PaymentController::class, 'receiptPayment'])->name('viewCustomerPayments.receipt');
        Route::get('/viewCustomerPayments/quarterlyReport', [PaymentController::class, 'showQuarterlyReport'])->name('viewCustomerPayments.quarterlyReport');
        Route::get('/viewCustomerPayments/annualReportCustomerPayments', [PaymentController::class, 'showAnnualReport'])->name('viewCustomerPayments.annualReportCustomerPayments');
    });

    Route::group(['middleware' => ['can:viewWaterConnections']], function () {
        Route::get('/viewCustomerWaterConnections', [WaterConnectionController::class, 'showCustomerWaterConnections'])->name('viewCustomerWaterConnections.index');
    });

    Route::group(['middleware'=> ['can:viewCustomerDebts']], function() {
        Route::get('/viewCustomerDebts', [DebtController::class, 'showCustomerDebts'])->name('viewCustomerDebts.index');
    });

    Route::group(['middleware' => ['can:viewEarningTypes']], function () {
        Route::get('/earningTypes', [EarningTypeController::class, 'index'])->name('earningTypes.index');
        Route::resource('earningTypes', EarningTypeController::class);
    });

    Route::group(['middleware' => ['can:viewGeneralEarning']], function () {
        Route::get('/generalEarnings', [GeneralEarningController::class, 'index'])->name('generalEarnings.index');
        Route::resource('generalEarnings', GeneralEarningController::class);
    });
});

Route::get('/expiredSubscriptions/expired', [TokenController::class, 'showExpired'])->name('expiredSubscriptions.expired');
Route::post('/expiredSubscriptions/expired', [TokenController::class, 'validateNewToken'])->name('validatetoken');
