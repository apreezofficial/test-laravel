<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProductUploadController;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    
    // Orders
    Route::apiResource('orders', OrderController::class)->only(['index', 'store']);
    
    // Product Management
    Route::post('products/upload', [ProductUploadController::class, 'store']);
    Route::apiResource('products', ProductController::class);

    // Reports
    Route::prefix('reports')->group(function () {
        Route::get('low-stock', [ReportController::class, 'lowStock']);
        Route::get('sales-summary', [ReportController::class, 'salesSummary']);
    });
});