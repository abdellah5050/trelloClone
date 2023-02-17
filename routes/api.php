<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\projectController;
use App\Http\Controllers\statusController;
use App\Http\Controllers\taskController;
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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
// Route::any('api/user',[LoginController::class,'login']);



Route::group(['middleware' => 'api','prefix' => 'auth'], function ($router) {

    Route::post('/register',[AuthController::class,'register']);
    Route::post('/login',[AuthController::class,'login'])->name('login');
    Route::get('/profile',[AuthController::class,'profile']);
    Route::get('/logout',[AuthController::class,'logout']);
});

Route::group(['middleware' => 'api','prefix' => 'project'], function ($router) {
    Route::get('/list',[projectController::class,'show']);
    Route::post('/create',[projectController::class,'create']);
    Route::get('/edit/{id}',[projectController::class,'edit']);
    Route::put('/update/{id}',[projectController::class,'update']);

});

Route::group(['middleware' => 'api','prefix' => 'task'], function ($router) {

    Route::get('/list',[taskController::class,'show']);
    Route::post('/{project}/create',[taskController::class,'create']);
    Route::put('/update/{task}',[taskController::class,'update']);
    Route::get('/edit/{task}',[taskController::class,'edit']);
    Route::get('/project/list/{project}',[taskController::class,'showtaskProject']);

});
Route::group(['middleware' => 'api','prefix' => 'status'], function ($router) {

    Route::get('/list',[statusController::class,'show']);
    Route::post('/create',[statusController::class,'create']);
    Route::get('/edit/{id}',[statusController::class,'edit']);
    Route::put('/update/{id}',[statusController::class,'update']);

});
