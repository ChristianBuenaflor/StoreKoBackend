<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\userController;
use App\Http\Controllers\AuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Authentication
Route::post('/login', [AuthController::class, 'login']);

// Users
Route::get('/users', [userController::class, 'index']);
Route::get('/users/{id}', [userController::class, 'show']);

Route::post('/create/users', [userController::class, 'store']);
Route::post('/update/users/{id}', [userController::class, 'update']);
Route::post('/archive/users/{id}', [userController::class, 'archive']);