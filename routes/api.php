<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\JwtMiddleware;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\BoardController;

Route::post('v1/register',[AuthController::class, 'register']);
Route::post('v1/refreshToken',[AuthController::class, 'refresh']);
Route::post('v1/login',[AuthController::class, 'login'])->name('login');
Route::post('v1/idCheck', [AuthController::class, 'idCheck']);
Route::middleware([JWTMiddleware::class])->group(function (){;
    Route::post('v1/user',[AuthController::class, 'getUser']);
    Route::post('v1/logout',[AuthController::class, 'logout']);
});
Route::middleware('guest')->group(function (){
    Route::get('board/all', [BoardController::class, 'boardAll']);
});

require __DIR__.'/admin.php';
require __DIR__.'/common.php';
