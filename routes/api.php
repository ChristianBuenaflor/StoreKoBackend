<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// GET
Route::get('/users', [UserController::class, 'index']);
Route::get('/users/{id}', [UserController::class, 'show']);

// POST
Route::post('/users', [UserController::class, 'store']);
Route::post('/users/{id}/update', [UserController::class, 'update']);
Route::post('/users/{id}/archive', [UserController::class, 'archive']);