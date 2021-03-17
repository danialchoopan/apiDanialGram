<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CommentsController;
use App\Http\Controllers\Api\LikesController;
use App\Http\Controllers\Api\PostController;
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
//auth JWT routes
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::get('logout', [AuthController::class, 'logout']);
Route::post('check_token', [AuthController::class, 'check_token'])->middleware('JWTauthToekn');
Route::post('userinfo', [AuthController::class, 'complete_user_info'])->middleware('JWTauthToekn');


Route::get('testuser1', function () {
    return response([[
        'id' => 1,
        'name' => 'dasdas',
        'family' => 'dasdas'
    ], [
        'id' => 2,
        'name' => 'name2fwq',
        'family' => 'fafwqmily2'
    ], [
        'id' => 3,
        'name' => 'nafwqfme2',
        'family' => 'familyfsa'
    ], [
        'id' => 4,
        'name' => 'name4',
        'family' => 'family4'
    ]]);
});


//auth requred routes
Route::middleware(['JWTauthToekn'])->group(function () {
    //Posts Routes
    Route::apiResource('posts', PostController::class);
    Route::apiResource('comments', CommentsController::class);
    Route::apiResource('likes', LikesController::class);


    //user
    Route::get('user/posts', [PostController::class, 'userPosts']);
});





// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
