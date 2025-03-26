<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CommonController;

Route::middleware('guest')->prefix('common')->group(function (){
    Route::get('/getMenuType', [CommonController::class, 'getMenuType']);
    Route::get('/getCombVisible', [CommonController::class, 'getCombVisible']);
});
