<?php

use App\Http\Controllers\AuthoController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('register', [AuthoController::class, 'register']);
Route::post('login', [AuthoController::class, 'login']);

Route::post('logout', [AuthoController::class, 'logout'])->middleware('auth:sanctum');
//Delete Account 
Route::post('delete-account', [AuthoController::class, 'deleteAccount'])->middleware('auth:sanctum');
//Reset Password
Route::post('reset-password', [AuthoController::class, 'resetPassword'])->middleware('auth:sanctum');




// create route group for sanctum middleware
Route::middleware('auth:sanctum')->group(function () {
    // create route group for category controller
    Route::apiResource('categories', CategoryController::class);
    // create route group for product controller
    Route::post('products', [ProductController::class, 'store']);
    Route::get('products', [ProductController::class, 'index']);
});
