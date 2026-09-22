<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\userController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// GET
Route::get('/users', [userController::class, 'index']);
Route::get('/users/{id}', [userController::class, 'show']);

// POST
Route::post('/users', [userController::class, 'store']);
Route::post('/users/{id}/update', [userController::class, 'update']);
Route::post('/users/{id}/archive', [userController::class, 'archive']);