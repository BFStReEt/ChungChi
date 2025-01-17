<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\FilesController;

//Admin

Route::post('/admin/manage', [AdminController::class, 'manage']);
Route::post('/admin/store', [AdminController::class, 'store']);
Route::post('/admin/login', [AdminController::class, 'login'])->name('admin.login');
Route::post('/admin/{id}', [AdminController::class, 'delete']);
Route::get('/admin/{id}', [AdminController::class, 'edit']);

Route::middleware('auth:admin')->group(function () {
    Route::post('/admin/logout', [AdminController::class, 'logout']);
});

//Category
Route::get('/categories/{categorySlug}/{subCategorySlug?}/{yearSlug?}', [CategoryController::class, 'show']);

//File
Route::post('/file/import/{categorySlug}/{subCategorySlug?}/{yearSlug?}', [FilesController::class, 'import']);
Route::post('/file/delete/{id}', [FilesController::class, 'delete']);
Route::get('/file/download/{id}', [FilesController::class, 'download']);

//Check
use App\Http\Controllers\AuthController;

Route::get('/checkToken', [AuthController::class, 'checkToken']);
