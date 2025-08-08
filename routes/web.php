<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\DashboardController;
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('dashboard/store', [App\Http\Controllers\StoreDashboardController::class, 'index'])->name('dashboard.store');
    Route::get('orders/export', [App\Http\Controllers\OrderController::class, 'exportExcel'])->name('orders.exportExcel');
    Route::get('products/export', [App\Http\Controllers\ProductController::class, 'exportExcel'])->name('products.exportExcel');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rutas para administrar tiendas WooCommerce
    Route::resource('woocommerce_credentials', App\Http\Controllers\WooCommerceCredentialController::class)->except(['show', 'edit', 'update']);

    // Rutas para productos y pedidos de una tienda conectada
    Route::get('products', [App\Http\Controllers\ProductController::class, 'index'])->name('products.index');
    Route::get('orders', [App\Http\Controllers\OrderController::class, 'index'])->name('orders.index');
});

require __DIR__.'/auth.php';
