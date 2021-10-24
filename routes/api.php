<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\InvoiceController;

Route::post('login', [ApiController::class, 'authenticate']);
Route::post('register', [ApiController::class, 'register']);

Route::group(['middleware' => ['jwt.verify']], function() {
    Route::get('logout', [ApiController::class, 'logout']);
    Route::get('company', [CompanyController::class, 'getList']);
    Route::post('company', [CompanyController::class, 'add']);
    Route::post('invoice', [InvoiceController::class, 'add']);
    Route::post('complete-invoice', [InvoiceController::class, 'completeInvoice']);
});