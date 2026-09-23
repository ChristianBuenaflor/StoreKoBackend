<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\userController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\InventoryController;


// Authentication

Route::post('/login', [AuthController::class, 'login']);

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth:sanctum');


// Users

Route::get('/users', [userController::class, 'index']);

Route::get('/users/{id}', [userController::class, 'show']);

Route::post('/create/users', [userController::class, 'store']);

Route::post('/update/users/{id}', [userController::class, 'update']);

Route::post('/archive/users/{id}', [userController::class, 'archive']);


// Inventory

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/inventory', [InventoryController::class, 'index']);

    Route::get('/inventory/{id}', [InventoryController::class, 'show']);

    Route::post('/create/inventory', [InventoryController::class, 'store']);

    Route::post('/update/inventory/{id}', [InventoryController::class, 'update']);

    Route::post('/delete/inventory/{id}', [InventoryController::class, 'delete']);

});