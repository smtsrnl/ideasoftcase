<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\DiscountController;


Route::apiResource('orders', OrderController::class)->only(['index', 'store', 'destroy']);
Route::post('calculate-discount', [DiscountController::class, 'calculate']);
