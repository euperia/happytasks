<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\StatusController;
use App\Http\Controllers\API\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/register', [AuthController::class, 'register'])->name('api.register');
Route::post('/login', [AuthController::class, 'login'])->name('api.login');

Route::group(['middleware' => ['auth:sanctum']], function () {

    Route::get('/user', function (Request $request) {
        return $request->user();
    })->name('api.user');

    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');

    // Status routes

    Route::prefix('status')->group(function () {
        Route::get('/', [StatusController::class, 'index'])->name('api.status.list');
        Route::get('/{status}', [StatusController::class, 'show'])->name('api.status.get');
        Route::post('/', [StatusController::class, 'store'])->name('api.status.create');
        Route::put('/{status}', [StatusController::class, 'update'])->name('api.status.update');
        Route::delete('/{status}', [StatusController::class, 'destroy'])->name('api.status.delete');
    });

    // Category routes

    Route::prefix('category')->group(function () {
        Route::get('/{category}', [CategoryController::class, 'show'])->name('api.category.get');
        Route::get('/', [CategoryController::class, 'index'])->name('api.category.list');
        Route::post('/', [CategoryController::class, 'store'])->name('api.category.create');
        Route::put('/{category}', [CategoryController::class, 'update'])->name('api.category.update');
        Route::delete('/{category}', [CategoryController::class, 'delete'])->name('api.category.delete');
    });

    // Task routes
    Route::prefix('task')->group(function () {
        Route::get('/{task}', [TaskController::class, 'show'])->name('api.task.get');
        Route::get('/', [TaskController::class, 'index'])->name('api.task.list');
        Route::post('/', [TaskController::class, 'store'])->name('api.task.create');
        Route::patch('/{task}', [TaskController::class, 'update'])->name('api.task.update');
        Route::delete('/{task}', [TaskController::class, 'destroy'])->name('api.task.delete');
    });
});
