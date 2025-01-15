<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\QuyTrinhController;
use App\Models\Admin;
use App\Providers\AuthServiceProvider;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\FileController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:api');

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
Route::get('file/import/{categorySlug}/{subCategorySlug?}/{yearSlug?}', [FileController::class, 'import']);
//Quy trình
// Route::post('quytrinh/import', [QuyTrinhController::class, 'import']);
// Route::get('quytrinh/export', [QuyTrinhController::class, 'export']);
// Route::delete('quytrinh/delete/{id}', [QuyTrinhController::class, 'delete']);
// Route::post('quytrinh/find', [QuyTrinhController::class, 'findbyName']);


//Check
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\AuthController;

Route::get('/checkToken', [AuthController::class, 'checkToken']);

Route::post('/permissions', [PermissionController::class, 'index']);
