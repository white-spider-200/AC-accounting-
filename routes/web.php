<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ConfigurationController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\LocalizationController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\MeasureController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ExpensesCategoryController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ProductsCategoryController;
use App\Http\Controllers\ProductsBrandController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\VariantController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymentSaleController;
use App\Http\Controllers\AccountingController;
use App\Http\Controllers\VatRateController;

use App\Http\Controllers\AdjustmentController;
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

Auth::routes();

Route::controller(AdminController::class)->middleware(['auth'])->group(function () {
    Route::get('/', 'index' )->name('homephp');

});

Route::controller(ConfigurationController::class)->prefix('admin')->middleware(['auth','superadmin'])->group(function () {
    Route::get('/configurations', 'index');
	Route::post('/saveconfiguration', 'store');
	Route::post('/savelogo', 'process');
});
Route::controller(AdminController::class)->prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('/', 'index');
});
Route::controller(UserController::class)->prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('/users','index')->name('users.index')->middleware(['superadmin']);
    Route::put('/users/{user}','update')->name('users.update')->middleware(['superadmin']);
    Route::post('/users/store','store')->name('users.store')->middleware(['superadmin']);
    Route::delete('/users/{user}','delete')->name('users.delete')->middleware(['superadmin']);
    Route::get('/users/{user}', 'show')->name('users.show')->middleware(['superadmin']);
    Route::get('/users/{user}/edit', 'edit')->name('users.edit')->middleware(['superadmin']);
    Route::get('/user/create', 'create')->name('users.create')->middleware(['superadmin']);
    Route::get('/editMyAccount', 'editMyAccount')->name('users.editMyAccount');
    Route::post('/saveEditMyAccount', 'saveEditMyAccount')->name('users.saveEditMyAccount');
});
Route::controller(PermissionController::class)->prefix('admin')->middleware(['auth','superadmin'])->group(function () {
    Route::get('permissions/{id}', 'index')->name('permissions.index');
});
Route::controller(RoleController::class)->prefix('admin')->middleware(['auth'])->group(function () {
    Route::post('roles/{role}/assign', 'assign')->name('roles.assign');
    Route::get('roles', 'index')->name('roles.index');
});
Route::controller(LocalizationController::class)->prefix('admin')->group(function () {
    Route::get('lang/{locale}', 'lang')->name('lang');
});
Route::controller(WarehouseController::class)->prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('/warehouses', 'index' )->name('warehouses.index');
    Route::get('/warehouses/create', 'create' )->name('warehouses.create');
    Route::post('/warehouses', 'store' )->name('warehouses.store');
    Route::get('/warehouses/{warehouse}/edit', 'edit' )->name('warehouses.edit');
    Route::put('/warehouses/{warehouse}', 'update' )->name('warehouses.update');
    Route::delete('/warehouses/{warehouse}', 'destroy' )->name('warehouses.destroy');

});
Route::controller(MeasureController::class)->prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('/measures', 'index' )->name('measures.index');
    Route::get('/measures/create', 'create' )->name('measures.create');
    Route::post('/measures', 'store' )->name('measures.store');
    Route::get('/measures/{measure}/edit', 'edit' )->name('measures.edit');
    Route::put('/measures/{measure}', 'update' )->name('measures.update');
    Route::delete('/measures/{measure}', 'destroy' )->name('measures.destroy');

});
Route::controller(CurrencyController::class)->prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('/currencies', 'index' )->name('currencies.index');
    Route::get('/currencies/create', 'create' )->name('currencies.create');
    Route::post('/currencies', 'store' )->name('currencies.store');
    Route::get('/currencies/{currency}/edit', 'edit' )->name('currencies.edit');
    Route::put('/currencies/{currency}', 'update' )->name('currencies.update');
    Route::delete('/currencies/{currency}', 'destroy' )->name('currencies.destroy');

});
Route::controller(SupplierController::class)->prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('/suppliers', 'index' )->name('suppliers.index');
    Route::get('/suppliers/create', 'create' )->name('suppliers.create');
    Route::post('/suppliers', 'store' )->name('suppliers.store');
    Route::get('/suppliers/{supplier}/edit', 'edit' )->name('suppliers.edit');
    Route::put('/suppliers/{supplier}', 'update' )->name('suppliers.update');
    Route::delete('/suppliers/{supplier}', 'destroy' )->name('suppliers.destroy');

});
Route::controller(ClientController::class)->prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('/clients', 'index' )->name('clients.index');
    Route::get('/clients/create', 'create' )->name('clients.create');
    Route::post('/clients', 'store' )->name('clients.store');
    Route::get('/clients/{client}/edit', 'edit' )->name('clients.edit');
    Route::put('/clients/{client}', 'update' )->name('clients.update');
    Route::delete('/clients/{client}', 'destroy' )->name('clients.destroy');

});
Route::controller(ExpensesCategoryController::class)->prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('/expensescategories', 'index' )->name('expensescategories.index');
    Route::get('/expensescategories/create', 'create' )->name('expensescategories.create');
    Route::post('/expensescategories', 'store' )->name('expensescategories.store');
    Route::get('/expensescategories/{expensesCategory}/edit', 'edit' )->name('expensescategories.edit');
    Route::put('/expensescategories/{expensesCategory}', 'update' )->name('expensescategories.update');
    Route::delete('/expensescategories/{expensesCategory}', 'destroy' )->name('expensescategories.destroy');

});
Route::controller(ExpenseController::class)->prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('/expenses', 'index' )->name('expenses.index');
    Route::get('/expenses/create', 'create' )->name('expenses.create');
    Route::post('/expenses', 'store' )->name('expenses.store');
    Route::get('/expenses/{expense}/edit', 'edit' )->name('expenses.edit');
    Route::put('/expenses/{expense}', 'update' )->name('expenses.update');
    Route::delete('/expenses/{expense}', 'destroy' )->name('expenses.destroy');

});
Route::controller(ProductsCategoryController::class)->prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('/productscategories', 'index' )->name('productscategories.index');
    Route::get('/productscategories/create', 'create' )->name('productscategories.create');
    Route::post('/productscategories', 'store' )->name('productscategories.store');
    Route::get('/productscategories/{productsCategory}/edit', 'edit' )->name('productscategories.edit');
    Route::put('/productscategories/{productsCategory}', 'update' )->name('productscategories.update');
    Route::delete('/productscategories/{productsCategory}', 'destroy' )->name('productscategories.destroy');
    Route::post('/productscategories/process', 'process' )->name('productscategories.process');
});
Route::controller(ProductsBrandController::class)->prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('/productsbrands', 'index' )->name('productsbrands.index');
    Route::get('/productsbrands/create', 'create' )->name('productsbrands.create');
    Route::post('/productsbrands', 'store' )->name('productsbrands.store');
    Route::get('/productsbrands/{productsBrand}/edit', 'edit' )->name('productsbrands.edit');
    Route::put('/productsbrands/{productsBrand}', 'update' )->name('productsbrands.update');
    Route::delete('/productsbrands/{productsBrand}', 'destroy' )->name('productsbrands.destroy');
    Route::post('/productsbrands/process', 'process' )->name('productsbrands.process');
});
Route::controller(ProductController::class)->prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('/products', 'index' )->name('products.index');
    Route::get('/products/create', 'create' )->name('products.create');
    Route::post('/products', 'store' )->name('products.store');
    Route::get('/products/{product}/edit', 'edit' )->name('products.edit');
    Route::put('/products/{product}', 'update' )->name('products.update');
    Route::delete('/products/{product}', 'destroy' )->name('products.destroy');
    Route::post('/products/process', 'process' )->name('products.process');
    Route::get('/products/images', 'getProductImages' )->name('products.images');
    Route::get('/products/setimage', 'setImage' )->name('products.setimage');
    Route::get('/products/deleteimage', 'deleteImage' )->name('products.deleteimage');
    Route::get('/products/search', 'search' )->name('products.search');

});

Route::controller(VariantController::class)->prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('/variants', 'index' )->name('variants.index');
    Route::get('/variants/create', 'create' )->name('variants.create');
    Route::post('/variants', 'store' )->name('variants.store');
    Route::get('/variants/{variant}/edit', 'edit' )->name('variants.edit');
    Route::put('/variants/{variant}', 'update' )->name('variants.update');
    Route::delete('/variants/{variant}', 'destroy' )->name('variants.destroy');
});

Route::controller(PurchaseController::class)->prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('/purchases', 'index' )->name('purchases.index');
    Route::get('/purchases/create', 'create' )->name('purchases.create');
    Route::post('/purchases', 'store' )->name('purchases.store');
    Route::get('/purchases/{purchase}/edit', 'edit' )->name('purchases.edit');
    Route::put('/purchases/{purchase}', 'update' )->name('purchases.update');
    Route::delete('/purchases/{purchase}', 'destroy' )->name('purchases.destroy');

});
Route::controller(PaymentController::class)->prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('/payments', 'index' )->name('payments.index');
    Route::get('/payments/create', 'create' )->name('payments.create');
    Route::post('/payments', 'store' )->name('payments.store');
    Route::get('/payments/{payment}/edit', 'edit' )->name('payments.edit');
    Route::get('/payments/{id}', 'salePayments' )->name('payments.sale');
    Route::get('/payment/{id}', 'singlePayment' )->name('payments.singlePayment');
    Route::put('/payments/{payment}', 'update' )->name('payments.update');
    Route::delete('/payments/{payment}', 'destroy' )->name('payments.destroy');

});
Route::controller(PaymentSaleController::class)->prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('/paymentsales', 'index' )->name('paymentsales.index');
    Route::get('/paymentsales/create', 'create' )->name('paymentsales.create');
    Route::post('/paymentsales', 'store' )->name('paymentsales.store');
    Route::get('/paymentsales/{payment}/edit', 'edit' )->name('paymentsales.edit');
    Route::get('/paymentsales/{id}', 'salePayments' )->name('paymentsales.sale');
    Route::get('/paymentsale/{id}', 'singlePayment' )->name('paymentsales.singlePayment');
    Route::put('/paymentsales/{payment}', 'update' )->name('paymentsales.update');
    Route::delete('/paymentsales/{payment}', 'destroy' )->name('paymentsales.destroy');

});
Route::controller(AdjustmentController::class)->prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('/adjustments', 'index' )->name('adjustments.index');
    Route::get('/adjustments/create', 'create' )->name('adjustments.create');
    Route::post('/adjustments', 'store' )->name('adjustments.store');
    Route::get('/adjustments/{adjustment}/edit', 'edit' )->name('adjustments.edit');
    Route::put('/adjustments/{adjustment}', 'update' )->name('adjustments.update');
    Route::delete('/adjustments/{adjustment}', 'destroy' )->name('adjustments.destroy');
    Route::get('/adjustments/{adjustment}', 'show')->name('adjustments.show');

});

Route::controller(SaleController::class)->prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('/sales', 'index' )->name('sales.index');
    Route::get('/sales/create', 'create' )->name('sales.create');
    Route::post('/sales', 'store' )->name('sales.store');
    Route::get('/sales/{sale}/edit', 'edit' )->name('sales.edit');
    Route::put('/sales/{sale}', 'update' )->name('sales.update');
    Route::delete('/sales/{sale}', 'destroy' )->name('sales.destroy');
    Route::get('/sales/invoice/{id}', 'invoice' )->name('sales.invoice');
    Route::any('/sales/getproducts', 'getProductsByWarehouse' )->name('sales.products');

});

Route::controller(AccountingController::class)->prefix('admin/accounting')->middleware(['auth'])->group(function () {
    Route::get('/profit-loss', 'profitAndLoss')->name('accounting.profit-loss');
    Route::get('/accounts-receivable', 'accountsReceivable')->name('accounting.accounts-receivable');
    Route::get('/accounts-payable', 'accountsPayable')->name('accounting.accounts-payable');
    Route::get('/cashflow', 'cashflow')->name('accounting.cashflow');
    Route::get('/vat-summary', 'vatSummary')->name('accounting.vat-summary');
    Route::get('/gl-reports/trial-balance', 'trialBalance')->name('accounting.gl-reports.trial-balance');
    Route::get('/gl-reports/ledger', 'ledgerReport')->name('accounting.gl-reports.ledger');
    Route::get('/gl-reports/profit-loss', 'glProfitAndLoss')->name('accounting.gl-reports.profit-loss');
    Route::get('/gl-reports/balance-sheet', 'balanceSheet')->name('accounting.gl-reports.balance-sheet');
    Route::get('/gl-reports/vat-summary', 'glVatSummary')->name('accounting.gl-reports.vat-summary');
    Route::get('/gl-management/chart-of-accounts', 'chartOfAccounts')->name('accounting.gl-management.chart-of-accounts');
    Route::get('/gl-management/chart-of-accounts/create', 'createAccount')->name('accounting.gl-management.chart-of-accounts.create');
    Route::post('/gl-management/chart-of-accounts', 'storeAccount')->name('accounting.gl-management.chart-of-accounts.store');
    Route::get('/gl-management/chart-of-accounts/{account}/edit', 'editAccount')->name('accounting.gl-management.chart-of-accounts.edit');
    Route::put('/gl-management/chart-of-accounts/{account}', 'updateAccount')->name('accounting.gl-management.chart-of-accounts.update');
    Route::delete('/gl-management/chart-of-accounts/{account}', 'deleteAccount')->name('accounting.gl-management.chart-of-accounts.delete');
    Route::get('/gl-management/journal-entries', 'journalEntries')->name('accounting.gl-management.journal-entries');
    Route::get('/gl-management/journal-entries/create', 'createJournalEntry')->name('accounting.gl-management.journal-entries.create');
    Route::post('/gl-management/journal-entries', 'storeJournalEntry')->name('accounting.gl-management.journal-entries.store');
    Route::get('/gl-management/journal-entries/{entry}', 'showJournalEntry')->name('accounting.gl-management.journal-entries.show');
    Route::get('/gl-management/accounting-mappings', 'accountingMappings')->name('accounting.gl-management.accounting-mappings');
    Route::post('/gl-management/accounting-mappings', 'saveAccountingMappings')->name('accounting.gl-management.accounting-mappings.save');
    Route::get('/gl-management/opening-balances', 'openingBalances')->name('accounting.gl-management.opening-balances');
    Route::get('/gl-management/opening-balances/create', 'createOpeningBalance')->name('accounting.gl-management.opening-balances.create');
    Route::post('/gl-management/opening-balances', 'storeOpeningBalance')->name('accounting.gl-management.opening-balances.store');
    Route::get('/gl-management/periods', 'periods')->name('accounting.gl-management.periods');
    Route::get('/gl-management/periods/create', 'createPeriod')->name('accounting.gl-management.periods.create');
    Route::post('/gl-management/periods', 'storePeriod')->name('accounting.gl-management.periods.store');
    Route::get('/gl-management/periods/{period}/edit', 'editPeriod')->name('accounting.gl-management.periods.edit');
    Route::put('/gl-management/periods/{period}', 'updatePeriod')->name('accounting.gl-management.periods.update');
});

Route::controller(VatRateController::class)->prefix('admin/accounting')->middleware(['auth', 'superadmin'])->group(function () {
    Route::get('/gl-management/vat-rates', 'index')->name('accounting.gl-management.vat-rates');
    Route::post('/gl-management/vat-rates', 'store')->name('accounting.gl-management.vat-rates.store');
    Route::put('/gl-management/vat-rates/{vatRate}', 'update')->name('accounting.gl-management.vat-rates.update');
    Route::delete('/gl-management/vat-rates/{vatRate}', 'destroy')->name('accounting.gl-management.vat-rates.delete');
});
