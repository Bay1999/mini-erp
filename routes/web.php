<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::middleware('auth.user')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/data', [DashboardController::class, 'data'])->name('dashboard.data');

    Route::prefix('master')->name('master.')->group(function () {
        Route::get('user/data', [UserController::class, 'data'])->name('user.data');
        Route::resource("user", UserController::class);
        Route::get('item/data', [ItemController::class, 'data'])->name('item.data');
        Route::resource("item", ItemController::class);
    });

    Route::get('sales/data', [SalesController::class, 'data'])->name('sales.data');
    Route::resource("sales", SalesController::class);
    Route::get('payment/data', [PaymentController::class, 'data'])->name('payment.data');
    Route::resource("payment", PaymentController::class);

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
