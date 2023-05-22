<?php

use App\Http\Controllers\Api\BoardController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\api\FollowContorller;
use App\Http\Controllers\api\PinController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\SearchController;

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
Route::get('auth/check' , [AuthController::class , 'check']);
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('auth/logout' , [AuthController::class , 'logout']);
    Route::apiResource('user', UserController::class)->except('index');
    Route::put('/profile/{user}' , [UserController::class , 'setProfile']);
    Route::get('/profile/{user}' , [UserController::class , 'getProfile']);
    Route::post('/user/{user}/follow/{follow}' , [FollowContorller::class , 'toggleFollow']);
    Route::apiResource('pin', PinController::class);
    Route::get('/home', [PinController::class,'home']);
    Route::get('/pin/{pin}/image', [PinController::class,'pin']);
    Route::apiResource('pin.comment', CommentController::class)->only(['index', 'store','destroy']);
    Route::post('repin/{pin}',[PinController::class , 'repin']);
    Route::apiResource('category', CategoryController::class)->only(['index', 'show']);
    Route::apiResource('board', BoardController::class)->except('show');
    Route::get('/search/history', [SearchController::class, 'index']);
    Route::delete('/search/history/{id}', [SearchController::class, 'destroy']);
    Route::post('/search/suggestions', [SearchController::class, 'getSuggestions']);
    Route::post('/search', [SearchController::class, 'search']);
    Route::apiresource('chat',ChatController::class)->only(['index','show','destory']);
    Route::apiresource('message',MessageController::class)->only(['store','destroy']);
});
