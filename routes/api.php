<?php

use App\Http\Controllers\API\StatusController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;

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
    });
});
