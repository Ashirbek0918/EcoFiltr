<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\FilterController;

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

Route::post('login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('getme', [AuthController::class, 'getme']);
    Route::delete('logout', [AuthController::class, 'logout']);
    //user
    Route::post('user/create', [OrderController::class, 'create']);
    Route::put('user/update/{user}', [UserController::class, 'update']);
    Route::delete('user/delete/{user}', [UserController::class, 'delete']);
    //update description 
    Route::put('user/{user}', [OrderController::class, 'update']);
    Route::get('user/about/{user}', [UserController::class, 'about']);
    //filter
    Route::get('users/filters/all', [FilterController::class, 'getFilteredUsers']);
    Route::put('filter/update/{filter}', [FilterController::class, 'update']);
});
