<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::group(['prefix' => 'admin'], function () {

    Route::get('/', [DashboardController::class, 'index'])->name('admin.index');

    ROute::group(['prefix' => 'coa-category'], function () {
        Route::get('/', [\App\Http\Controllers\CoaCategoryController::class, 'index'])->name('coa-category.index');
        Route::get('/list', [\App\Http\Controllers\CoaCategoryController::class, 'coaCategoryList'])->name('coa-category.list');
        Route::get('/{id}', [\App\Http\Controllers\CoaCategoryController::class, 'coaCategoryDetails'])->name('coa-category.details');

        Route::post('/', [\App\Http\Controllers\CoaCategoryController::class, 'createCoaCategory'])->name('coa-category.store');

        Route::delete('/{id}', [\App\Http\Controllers\CoaCategoryController::class, 'deleteCoaCategory'])->name('coa-category.delete');

        Route::put('/{id}', [\App\Http\Controllers\CoaCategoryController::class, 'updateCoaCategory'])->name('coa-category.update');
    });

});
