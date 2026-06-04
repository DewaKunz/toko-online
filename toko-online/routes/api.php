<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\TransactionController;
use App\Http\Controllers\API\UserController;
use Illuminate\Support\Facades\Route;

// ─── Public Routes (Tidak butuh token) ────────────────────────────────────
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// ─── Protected Routes (Wajib pakai token Sanctum) ─────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);

    // Users
    Route::get('/users',          [UserController::class, 'index']);
    Route::get('/users/{id}',     [UserController::class, 'show']);
    Route::put('/users/{id}',     [UserController::class, 'update']);
    Route::delete('/users/{id}',  [UserController::class, 'destroy']);

    // Products
    Route::get('/products',          [ProductController::class, 'index']);
    Route::post('/products',         [ProductController::class, 'store']);
    Route::get('/products/{id}',     [ProductController::class, 'show']);
    Route::put('/products/{id}',     [ProductController::class, 'update']);
    Route::delete('/products/{id}',  [ProductController::class, 'destroy']);

    // Transactions
    Route::get('/transactions',          [TransactionController::class, 'index']);
    Route::post('/transactions',         [TransactionController::class, 'store']);
    Route::get('/transactions/{id}',     [TransactionController::class, 'show']);
    Route::put('/transactions/{id}',     [TransactionController::class, 'update']);
    Route::delete('/transactions/{id}',  [TransactionController::class, 'destroy']);
});