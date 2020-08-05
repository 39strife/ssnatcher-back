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


Route::group(['prefix' => 'auth'], function () {
    Route::post('register', 'UserController@register');
    Route::post('authenticate', 'UserController@authenticate');
    Route::post('login', 'UserController@login');
    Route::get('refresh', 'UserController@refresh');
});

Route::apiResource("combos", "ComboController");
Route::apiResource("comment", "CommentController");

Route::group(['prefix' => 'combos'], function () {
    Route::post('{combo}/comment', "ComboController@comment");
});

Route::group(['prefix' => 'list'], function () {
    Route::get('games', 'ListController@games');
    Route::get('properties/{game?}', 'ListController@properties');
});
