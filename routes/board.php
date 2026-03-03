<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BoardController;
use App\Http\Middleware\JwtMiddleware;

Route::get('board/getPage/{menuId}', [BoardController::class, 'getPage']);
Route::post('board/getBoard', [BoardController::class, 'getBoard']);
Route::get('board/getBoard/{boardId}', [BoardController::class, 'getBoardById']);
Route::middleware(JwtMiddleware::class)->prefix('board')->group(function (){
    Route::post('/savePage', [BoardController::class, 'savePage']);
    Route::post('/getRoleByUser', [BoardController::class, 'getRoleByUser']);
    Route::post('/insertBoard', [BoardController::class, 'insertBoard']);
});
