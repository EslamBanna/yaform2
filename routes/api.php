<?php

use App\Http\Controllers\Controller;
use App\Http\Controllers\FormContoller;
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
    Route::post('/get-reset-password-code', [UserContoller::class, 'getResetPasswordCode']);
    Route::post('/update-password', [UserContoller::class, 'updatePassword']);

});


Route::group(['prefix' => 'auth', 'middleware' => 'checkAuth:api-user'], function () {
    Route::get('/get-my-info', [UserContoller::class, 'me']);
    Route::post('/logout', [UserContoller::class, 'logout']);
    Route::post('/update-my-info', [UserContoller::class, 'updateMyInfo']);
    Route::post('/create-template',[FormContoller::class,'createTemplate']);
    Route::get('/get-templates',[FormContoller::class,'getTemplates']);
    Route::get('/get-template/{templateId}',[FormContoller::class,'getTemplate']);


    Route::Post('/accept-response/{formId}',[FormContoller::class,'acceptResponse']);
    Route::Post('/form-setting/{formId}',[FormContoller::class,'formSetting']);
});

Route::get('/test',[Controller::class,'test']);
Route::post('/test',[Controller::class,'testT']);