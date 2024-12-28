<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LikeController;

Route::get('/', [ItemController::class, 'index'])->name('items.index');

Route::get('/item/{itemId}',[ItemController::class, 'show'])->name('items.show');
Route::post('/item/{itemId}/like', [LikeController::class, 'toggle'])->name('likes.toggle');
