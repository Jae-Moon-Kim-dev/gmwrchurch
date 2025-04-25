<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CommonController;
use App\Http\Middleware\JwtMiddleware;

Route::middleware(JwtMiddleware::class)->prefix('common')->group(function (){
    Route::get('/getMenuType', [CommonController::class, 'getMenuType']);
    Route::get('/getCombVisible', [CommonController::class, 'getCombVisible']);
    Route::get('/getRoles', [CommonController::class, 'getRoles']);
});
