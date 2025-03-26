<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BoardController;

Route::post('v1/register',[AuthController::class, 'register']);
Route::post('v1/login',[AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function (){
    Route::post('v1/logout',[AuthController::class, 'logout']);
});
Route::middleware('guest')->group(function (){
    Route::get('board/all', [BoardController::class, 'boardAll']);
});

require __DIR__.'/admin.php';
require __DIR__.'/common.php';
