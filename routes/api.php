<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Providers\AuthServiceProvider;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

Route::post('/admin/store', [AdminController::class, 'store']);
Route::post('/admin/login', [AdminController::class, 'login']);
Route::post('/admin/logout', [AdminController::class, 'logout']);



use App\Http\Controllers\PermissionController;
use App\Http\Controllers\AuthController;

Route::get('/checkToken', [AuthController::class, 'checkToken']);

Route::post('/permissions', [PermissionController::class, 'index']);
