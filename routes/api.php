<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::post('v1/register',[AuthController::class, 'register']);
Route::post('v1/login',[AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function (){
    Route::post('v1/logout',[AuthController::class, 'logout']);
});
// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

