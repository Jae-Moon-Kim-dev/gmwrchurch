<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileController;
use App\Http\Middleware\JwtMiddleware;

Route::middleware(JwtMiddleware::class)->prefix('file')->group(function (){
    Route::post('/upload', [FileController::class, 'upload']);
});
