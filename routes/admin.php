<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminMenuController;
use App\Http\Middleware\JwtMiddleware;

Route::middleware(JwtMiddleware::class)->prefix('admin/menus')->group(function (){
    Route::get('/', [AdminMenuController::class, 'index']);
    Route::get('/{id}', [AdminMenuController::class, 'show']);
    Route::post('/saveMenu', [AdminMenuController::class, 'store']);
    Route::patch('/saveMenu/{id}', [AdminMenuController::class, 'update']);
    Route::post('/updateMenuOrder', [AdminMenuController::class, 'updateOrder']);
    Route::delete('/deleteMenu/{id}', [AdminMenuController::class, 'destroy']);
});
