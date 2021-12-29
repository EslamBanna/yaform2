<?php

use App\Http\Controllers\UserContoller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



Route::group(['prefix' => 'unauth'], function () {
    Route::post('/sign-up', [UserContoller::class, 'signUp']);
    Route::post('/login', [UserContoller::class, 'login']);
    Route::post('/forget-password', [UserContoller::class, 'forgetPassword']);
});



Route::group(['prefix' => 'auth', 'middleware' => 'checkAuth:api'], function () {
    Route::get('/test', [UserContoller::class, 'test']);
    Route::post('/logout', [UserContoller::class, 'logout']);
});
