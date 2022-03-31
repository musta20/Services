<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ServicesController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\MessagesController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\RequirmenUploaderController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\UploadedFileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FileMangerController;
use App\Http\Controllers\filetoreqController;
use App\Http\Controllers\catController;


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


Route::resource('Follow',FollowController::class);
Route::resource('Messages',MessagesController::class);
Route::resource('Request',RequestController::class);
Route::resource('RequirmenUploader',RequirmenUploaderController::class);
Route::resource('UploadedFile',UploadedFileController::class);
Route::resource('user',UserController::class);
Route::resource('services',ServicesController::class);
Route::resource('Rating',RatingController::class);
Route::resource('imgetorequest',filetoreqController::class);
Route::resource('FileUpload',FileMangerController::class);
Route::resource('Categories',catController::class);


Route::post('ReSetSession',[userController::class,'ReSetSession']);


Route::get('showPublicImge/{id}',[FileMangerController::class,'showPublicImge']);

Route::get('showImge/{id}',[FileMangerController::class,'showImge']);


Route::get('showServicesBycompany/{id}',[ServicesController::class,'showServicesBycompany']);
Route::post('showServicesByCat',[ServicesController::class,'showServicesByCat']);
Route::get('getMycompany',[ServicesController::class,'getMycompany']);
Route::post('Search',[ServicesController::class,'Search']);
Route::post('Register',[userController::class,'Register']);
Route::post('logout',[userController::class,'logout']);
Route::post('Login',[userController::class,'Login'])->name('login');
Route::get('getImgeRequest/{id}',[UploadedFileController::class,'getImgeRequest']);


Route::get('downloadImge/{id}',[UploadedFileController::class,'downloadImge']);
Route::get('getRequestImge/{id}',[UploadedFileController::class,'getRequestImge']);



Route::get('GetFilsForReq/{id}',[UploadedFileController::class,'GetFilsForReq']);
Route::get('foreachallfile',[FileMangerController::class,'foreachallfile']);

Route::put('completeTask/{id}',[RequestController::class,'completeTask']);

Route::put('isViewed/{id}',[MessagesController::class,'isViewedMessages']);
Route::get('getNotfy',[MessagesController::class,'getNotfy']);

Route::get('isViewedRequests',[RequestController::class,'isViewedRequests']);

//completeTask

/// user 3 token Bearer 8|PHsKayEgMyCSDa6MxUAfvgTfHlzr726DcjvpcbW1
///
/// user 2 token Bearer 6|W7Z23a3MsbNbBj90hQi8EMgORuSUNi72XrCp22gk 
///
/// user 1 token Bearer 4|b9mIByTs21CDqlxhWJanwvrXMOdjB6zzw4edPpiH

