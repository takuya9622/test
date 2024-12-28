<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LikeController;

Route::get('/', [ItemController::class, 'index'])->name('items.index');

Route::get('/item/{itemId}',[ItemController::class, 'show'])->name('items.show');
Route::post('/item/{itemId}/like', [LikeController::class, 'toggle'])->name('likes.toggle');

Route::middleware(['auth'])->group(function () {
    Route::get('/purchase/{itemId}', [OrderController::class, 'create'])->name('purchase.create');
    Route::post('/order/store/{itemId}', [OrderController::class, 'store'])->name('order.store');
    Route::get('/success', [OrderController::class, 'success'])->name('checkout.success');
    Route::get('/cancel', [OrderController::class, 'cancel'])->name('checkout.cancel');
});