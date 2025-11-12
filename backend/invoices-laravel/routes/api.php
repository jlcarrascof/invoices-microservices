<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\InvoiceItemController;

// Health check endpoint
Route::get('/health', function () {
    return response()->json(['status' => 'ok', 'message' => 'Invoices API is running']);
});

// Customers CRUD
Route::apiResource('customers', CustomerController::class);

// Invoices CRUD
Route::apiResource('invoices', InvoiceController::class);

// Nested routes for invoice items (managing items within an invoice)
Route::prefix('invoices/{invoice}')->group(function () {
    Route::get('items', [InvoiceItemController::class, 'index']);
    Route::post('items', [InvoiceItemController::class, 'store']);
    Route::get('items/{item}', [InvoiceItemController::class, 'show']);
    Route::put('items/{item}', [InvoiceItemController::class, 'update']);
    Route::delete('items/{item}', [InvoiceItemController::class, 'destroy']);
});
