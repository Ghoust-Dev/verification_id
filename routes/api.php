<?php

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

Route::group(['middleware' => ['cors', 'json.response']], function () {

    Route::post('/checkIdExist', 'App\Http\Controllers\SaveDataUser@checkIdExist')->name('checkIdExist.api');
    Route::post('/uploadPhoto', 'App\Http\Controllers\SaveDataUser@uploadPhoto')->name('uploadPhoto.api');
    Route::post('/uploadVideo', 'App\Http\Controllers\SaveDataUser@uploadVideo')->name('uploadVideo.api');

});


