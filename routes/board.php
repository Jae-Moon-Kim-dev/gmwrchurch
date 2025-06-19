<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BoardController;
use App\Http\Middleware\JwtMiddleware;

Route::get('board/getPage/{menuId}', [BoardController::class, 'getPage']);
Route::middleware(JwtMiddleware::class)->prefix('board')->group(function (){
    Route::post('/savePage', [BoardController::class, 'savePage']);
});
