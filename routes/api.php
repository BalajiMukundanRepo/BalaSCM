<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\ConnectedAccountController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::post('login', [LoginController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('user', function (Request $request) {
        return $request->user();
    });

    Route::post('logout', [LoginController::class, 'logout']);

    // Activities
    Route::get('activities', [ActivityController::class, 'index']);

    // Clients (CRUD)
    Route::apiResource('clients', \App\Http\Controllers\ClientController::class);

    // Invoices (CRUD)
    Route::apiResource('invoices', \App\Http\Controllers\InvoiceController::class);

    // Payments (CRUD)
    Route::apiResource('payments', \App\Http\Controllers\PaymentController::class);

    // Products (CRUD)
    Route::apiResource('products', \App\Http\Controllers\ProductController::class);

    // Users
    Route::apiResource('users', \App\Http\Controllers\UserController::class);

    // Bank Transactions
    Route::apiResource('bank_transactions', \App\Http\Controllers\BankTransactionController::class);

    // Connected Accounts
    Route::post('connected_accounts', [ConnectedAccountController::class, 'store']);
    Route::delete('connected_accounts', [ConnectedAccountController::class, 'destroy']);
});
