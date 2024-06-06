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
    Route::post('user/create', [UserController::class, 'create']);
    Route::put('user/update/{user}', [UserController::class, 'update']);
    Route::delete('user/delete/{user}', [UserController::class, 'delete']);
    Route::get('users/{user}', [UserController::class, 'about']);

    //order 
    Route::post('order/create', [OrderController::class, 'create']);
    Route::put('order/update/{order}', [OrderController::class, 'update']);
    Route::get('orders/archive', [OrderController::class, 'archiveOrders']);
    Route::delete('order/delete/{order}', [OrderController::class, 'delete']);

    //filter
    Route::get('users/filters/all', [FilterController::class, 'all']);
    Route::get('users/filters/{filterType}', [FilterController::class, 'getFilteredUsers']);
    Route::delete('filter/delete/{filter}', [FilterController::class, 'delete']);


    Route::post('comment/create', [CommentController::class, 'create']);
    Route::put('comment/update/{comment}', [CommentController::class, 'update']);
    Route::delete('comment/delete/{comment}', [CommentController::class, 'delete']);

});
