<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoutingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoicesController;
use App\Http\Controllers\CapitalController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\InvestorController;

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

require __DIR__ . '/auth.php';

// Dashboard routes (require authentication)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    // الفواتير
    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoices/create', [InvoiceController::class, 'create'])->name('invoices.create');
    Route::get('/invoices/sold/list', [InvoiceController::class, 'sold'])->name('invoices.sold');
    Route::post('/invoices', [InvoiceController::class, 'store'])->name('invoices.store');
    Route::get('/invoices/{id}', [InvoiceController::class, 'show'])->name('invoices.show');
    Route::get('/invoices/{id}/edit', [InvoiceController::class, 'edit'])->name('invoices.edit');
    Route::put('/invoices/{id}', [InvoiceController::class, 'update'])->name('invoices.update');
    Route::delete('/invoices/{id}', [InvoiceController::class, 'destroy'])->name('invoices.destroy');
    Route::get('/invoices/{id}/sell', [InvoiceController::class, 'sellForm'])->name('invoices.sell-form');
    Route::post('/invoices/{id}/sell', [InvoiceController::class, 'sell'])->name('invoices.sell');

    // العملاء
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/{id}', [CustomerController::class, 'show'])->name('customers.show');
    Route::get('/customers/{id}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
    Route::put('/customers/{id}', [CustomerController::class, 'update'])->name('customers.update');
    Route::delete('/customers/{id}', [CustomerController::class, 'destroy'])->name('customers.destroy');
    Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');

    // API للفواتير المباعة
    Route::delete('/invoice-sales/{saleId}', [InvoiceController::class, 'destroySale'])->name('invoice-sales.destroy');

    // رأس المال
    Route::get('/capital', [CapitalController::class, 'index'])->name('capital.index');
    Route::get('/capital/transactions', [CapitalController::class, 'transactions'])->name('capital.transactions');
    Route::post('/capital/deposit', [CapitalController::class, 'deposit'])->name('capital.deposit');
    Route::post('/capital/withdraw', [CapitalController::class, 'withdraw'])->name('capital.withdraw');

    // المستثمرين
    Route::resource('investors', InvestorController::class);
    Route::post('/investors/deposit', [InvestorController::class, 'deposit'])->name('investors.deposit');
    Route::post('/investors/withdraw', [InvestorController::class, 'withdraw'])->name('investors.withdraw');
    Route::post('/investors/profit', [InvestorController::class, 'addProfit'])->name('investors.profit');
});

Route::group(['prefix' => '/'], function () {
    Route::get('', [RoutingController::class, 'index'])->name('root');
    Route::get('{first}/{second}/{third}', [RoutingController::class, 'thirdLevel'])->name('third');
    Route::get('{first}/{second}', [RoutingController::class, 'secondLevel'])->name('second');
    Route::get('{any}', [RoutingController::class, 'root'])->name('any');
});

