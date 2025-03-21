<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminMenuController;

Route::middleware('guest')->group(function (){
    Route::get('admin/menus', [AdminMenuController::class, 'index']);
});
