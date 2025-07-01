<?php

use App\Http\Controllers\AuthoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('register', [AuthoController::class, 'register']);
Route::post('login', [AuthoController::class, 'login']);

Route::post('logout', [AuthoController::class, 'logout'])->middleware('auth:sanctum');