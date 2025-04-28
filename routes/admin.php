<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminMenuController;
use App\Http\Controllers\Admin\AdminRoleController;
use App\Http\Controllers\Admin\AdminMemberController;
use App\Http\Middleware\JwtMiddleware;

Route::middleware(JwtMiddleware::class)->prefix('admin/menus')->group(function (){
    Route::get('/', [AdminMenuController::class, 'index']);
    Route::get('/{id}', [AdminMenuController::class, 'show']);
    Route::post('/saveMenu', [AdminMenuController::class, 'store']);
    Route::patch('/saveMenu/{id}', [AdminMenuController::class, 'update']);
    Route::post('/updateMenuOrder', [AdminMenuController::class, 'updateOrder']);
    Route::delete('/deleteMenu/{id}', [AdminMenuController::class, 'destroy']);
});

Route::middleware(JwtMiddleware::class)->prefix('admin/role')->group(function (){
    Route::get('/', [AdminRoleController::class, 'index']);
    Route::post('/', [AdminRoleController::class, 'store']);
    Route::put('/{id}', [AdminRoleController::class, 'update']);
    Route::delete('/{id}', [AdminRoleController::class, 'destroy']);
});

Route::middleware(JwtMiddleware::class)->prefix('admin/member')->group(function (){
    Route::post('/', [AdminMemberController::class, 'index']);
    Route::post('/updateMemberRole', [AdminMemberController::class, 'updateMemberRole']);
    Route::post('/deleteMember', [AdminMemberController::class, 'deleteMember']);
});
