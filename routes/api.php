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
    Route::post('{combo}/rate', "ComboController@rate");
});

Route::group(['prefix' => 'list'], function () {
    Route::get('games', 'ListController@games');
    Route::get('properties/{game?}', 'ListController@properties');
    Route::get('characters/{game?}', 'ListController@characters');
});

Route::group(['prefix' => 'profile'], function () {
    Route::get('me', 'UserController@me');
    Route::get('{user}', 'UserController@profile');
    Route::post('update', 'UserController@profileUpdate');
});



Route::group(['prefix' => 'character'], function () {
    Route::get("/", "CharacterController@index");
    Route::post("/{character}", "CharacterController@update");
    Route::get("/{character}", "CharacterController@show");
});
Route::group(['prefix' => 'games'], function () {
    Route::get("/", "GameController@index");
    Route::post("/{game}", "GameController@update");
    Route::get("/{game}", "GameController@show");
});
