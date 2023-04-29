<?php

use App\Http\Controllers\api\FollowContorller;
use App\Http\Controllers\api\PinController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

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

Route::post('auth/register', [AuthController::class , 'register']);
Route::post('auth/login' , [AuthController::class , 'login']);
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('auth/logout' , [AuthController::class , 'logout']);
    Route::get('/user' , [UserController::class , 'show']);
    Route::put('/user/{user}' , [UserController::class , 'update']);
    Route::delete('/user/{user}' , [UserController::class , 'destroy']);
    Route::put('/profile/{user}' , [UserController::class , 'setProfile']);
    Route::get('/profile' , [UserController::class , 'getProfile']);
    Route::post('/user/{user}/follow/{follow}' , [FollowContorller::class , 'toggleFollow']);
    Route::apiResource('pin', PinController::class)->except(['create', 'edit']);
});
