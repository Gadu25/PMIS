<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\DivisionController;
use App\Http\Controllers\Api\ProgramController;
use App\Http\Controllers\Api\WorkshopController;

Route::controller(AuthController::class)->group(function(){
    Route::post('login', 'login');
    Route::post('register', 'register');
});

Route::prefix('/user')->group(function(){
    Route::middleware('auth:sanctum')->get('/auth', [UserController::class, 'authUser']);
});

Route::prefix('/division')->group(function(){
    Route::middleware('auth:sanctum')->get('/', [DivisionController::class, 'index']);
});

Route::prefix('/program')->group(function(){
    Route::middleware('auth:sanctum')->get('/', [ProgramController::class, 'index']);
});

Route::prefix('/workshop')->group(function(){
    Route::middleware('auth:sanctum')->get('/', [WorkshopController::class, 'index']);
    Route::middleware('auth:sanctum')->post('/', [WorkshopController::class, 'store']);
    Route::middleware('auth:sanctum')->put('/{id}', [WorkshopController::class, 'update']);
    Route::middleware('auth:sanctum')->delete('/{id}', [WorkshopController::class, 'destroy']);
});