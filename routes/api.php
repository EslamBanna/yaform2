<?php

use App\Http\Controllers\AnswerContoller;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ExportController;
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
    Route::post('/create-template', [FormContoller::class, 'createTemplate']);
    Route::post('/append-template/{template_id}', [FormContoller::class, 'appendTemplate']);
    Route::get('/get-templates', [FormContoller::class, 'getTemplates']);
    Route::get('/get-template/{templateId}', [FormContoller::class, 'getTemplate']);

    Route::post('/create-form', [FormContoller::class, 'createForm']);
    Route::post('/append-form/{form_id}', [FormContoller::class, 'appendForm']);
    Route::get('/get-forms', [FormContoller::class, 'getForms']);
    Route::post('/send-form', [FormContoller::class, 'sendForm']);
    Route::delete('/delete-form/{formId}', [FormContoller::class, 'deleteFormOrQuiz']);
    Route::put('/update-form/{formId}', [FormContoller::class, 'updateForm']);
    Route::put('/append-update-form/{formId}', [FormContoller::class, 'appendUpdateForm']);

    Route::post('/create-quiz', [FormContoller::class, 'createQuiz']);
    Route::post('/append-quiz/{quiz_id}', [FormContoller::class, 'appendQuiz']);
    // Route::delete('/delete-quiz/{quizId}', [FormContoller::class,'deleteFormOrQuiz']);
    Route::put('/update-quiz/{quizId}', [FormContoller::class, 'updateQuiz']);

    Route::put('/accept-response/{formId}', [FormContoller::class, 'acceptResponse']);
    Route::put('/form-setting/{formId}', [FormContoller::class, 'formSetting']);

    Route::get('/get-summary-responses/{formId}', [AnswerContoller::class, 'getSummaryResponses']);
    Route::get('/get-question-responses/{questionId}', [AnswerContoller::class, 'getQuestionResponses']);
    Route::get('/get-individual-responses/{submitId}', [AnswerContoller::class, 'getIndividualResponses']);


    Route::get('/export-excel/{formId}', [ExportController::class, 'exportExcel']);
    Route::get('/show-pdf/{formId}', [ExportController::class, 'showPdf']);
    Route::get('/export-pdf/{formId}', [ExportController::class, 'exportPdf']);
});



Route::get('/export-vcf/{formId}', [ExportController::class, 'exportVcf']);

Route::get('/get-form/{formId}', [FormContoller::class, 'getForm']);
Route::post('/submit-answer', [AnswerContoller::class, 'submitAnswer']);
Route::post('/upload-image', [AnswerContoller::class, 'uploadImage']);
