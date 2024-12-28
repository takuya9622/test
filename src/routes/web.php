<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;

Route::get('/', [ItemController::class, 'index'])->name('items.index');

Route::get('/item/{itemId}',[ItemController::class, 'show'])->name('items.show');
