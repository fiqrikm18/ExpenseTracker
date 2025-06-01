<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::group(['prefix' => 'admin'], function () {

    Route::get('/', [DashboardController::class, 'index'])->name('admin.index');

    Route::group(['prefix' => 'coa-category'], function () {
        Route::get('/', [\App\Http\Controllers\CoaCategoryController::class, 'index'])->name('coa-category.index');
        Route::post('/', [\App\Http\Controllers\CoaCategoryController::class, 'createCoaCategory'])->name('coa-category.store');
        Route::get('/list', [\App\Http\Controllers\CoaCategoryController::class, 'coaCategoryList'])->name('coa-category.list');
        Route::get('/{id}', [\App\Http\Controllers\CoaCategoryController::class, 'coaCategoryDetails'])->name('coa-category.details');
        Route::delete('/{id}', [\App\Http\Controllers\CoaCategoryController::class, 'deleteCoaCategory'])->name('coa-category.delete');
        Route::put('/{id}', [\App\Http\Controllers\CoaCategoryController::class, 'updateCoaCategory'])->name('coa-category.update');
    });

    Route::group(['prefix' => 'coa'], function () {
        Route::get('/', [\App\Http\Controllers\CoaController::class, 'index'])->name('coa.index');
        Route::post('/', [\App\Http\Controllers\CoaController::class, 'createCoa'])->name('coa.store');
        Route::get('/list', [\App\Http\Controllers\CoaController::class, 'coaList'])->name('coa.list');
        Route::get('/{id}', [\App\Http\Controllers\CoaController::class, 'getCoa'])->name('coa.details');
        Route::delete('/{id}', [\App\Http\Controllers\CoaController::class, 'deleteCoa'])->name('coa.delete');
        Route::put('/{id}', [\App\Http\Controllers\CoaController::class, 'updateCoa'])->name('coa.update');
    });

    Route::group(['prefix' => 'transaction'], function () {
        Route::get('/', [\App\Http\Controllers\TransactionController::class, 'index'])->name('transaction.index');
        Route::post('/', [\App\Http\Controllers\TransactionController::class, 'createTransaction'])->name('transaction.store');
        Route::get('/list', [\App\Http\Controllers\TransactionController::class, 'transactionList'])->name('transaction.list');
        Route::get('/chart', [\App\Http\Controllers\TransactionController::class, 'transactionChart'])->name('transaction.chart');
        Route::get('/report', [\App\Http\Controllers\TransactionController::class, 'transactionReport'])->name('transaction.report');
        Route::get('/export', [\App\Http\Controllers\TransactionController::class, 'exportTransaction'])->name('transaction.export');
        Route::get('/{id}', [\App\Http\Controllers\TransactionController::class, 'transactionDetail'])->name('transaction.details');
        Route::delete('/{id}', [\App\Http\Controllers\TransactionController::class, 'deleteTransaction'])->name('transaction.delete');
        Route::put('/{id}', [\App\Http\Controllers\TransactionController::class, 'updateTransaction'])->name('transaction.update');
    });

});
