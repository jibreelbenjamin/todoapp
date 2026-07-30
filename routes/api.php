<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\SRCategoryController;
use App\Http\Controllers\Api\SRTaskController;
use App\Http\Controllers\Api\TaskController;
use Illuminate\Support\Facades\Route;

// Route::resource('open/category', CategoryController::class);

Route::get('open/task', [TaskController::class, 'index']);
Route::post('open/task', [TaskController::class, 'store']);
Route::get('open/task/{id}', [TaskController::class, 'show']);
Route::put('open/task/{id}', [TaskController::class, 'update']);
Route::delete('open/task/{id}', [TaskController::class, 'destroy']);

Route::get('open/category', [CategoryController::class, 'index']);
Route::post('open/category', [CategoryController::class, 'store']);
Route::get('open/category/{id}', [CategoryController::class, 'show']);
Route::put('open/category/{id}', [CategoryController::class, 'update']);
Route::delete('open/category/{id}', [CategoryController::class, 'destroy']);

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('me', [AuthController::class, 'profile']);
    Route::post('logout', [AuthController::class, 'logout']);

    Route::get('task', [TaskController::class, 'index']);
    Route::post('task', [TaskController::class, 'store']);
    Route::get('task/{id}', [TaskController::class, 'show']);
    Route::put('task/{id}', [TaskController::class, 'update']);
    Route::delete('task/{id}', [TaskController::class, 'destroy']);

    Route::get('category', [CategoryController::class, 'index']);
    Route::post('category', [CategoryController::class, 'store']);
    Route::get('category/{id}', [CategoryController::class, 'show']);
    Route::put('category/{id}', [CategoryController::class, 'update']);
    Route::delete('category/{id}', [CategoryController::class, 'destroy']);

    // implementasi sevice dan repo
    Route::post('sr/task/bulk/delete', [SRTaskController::class, 'bulkDestroy']);
    Route::post('sr/task/bulk/status', [SRTaskController::class, 'bulkStatus']);
    Route::get('sr/task', [SRTaskController::class, 'index']);
    Route::post('sr/task', [SRTaskController::class, 'store']);
    Route::get('sr/task/{id}', [SRTaskController::class, 'show']);
    Route::put('sr/task/{id}', [SRTaskController::class, 'update']);
    Route::delete('sr/task/{id}', [SRTaskController::class, 'destroy']);

    Route::post('sr/category/bulk/delete', [SRCategoryController::class, 'bulkDestroy']);
    Route::get('sr/category', [SRCategoryController::class, 'index']);
    Route::post('sr/category', [SRCategoryController::class, 'store']);
    Route::get('sr/category/{id}', [SRCategoryController::class, 'show']);
    Route::put('sr/category/{id}', [SRCategoryController::class, 'update']);
    Route::delete('sr/category/{id}', [SRCategoryController::class, 'destroy']);
});
