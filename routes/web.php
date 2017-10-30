<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::group(['namespace' => 'Admin', 'middleware' => 'auth'], function () {
    Route::group(['prefix' => 'user'], function () {
        Route::get('/', 'UserController@index');
        Route::get('/{user_id}', 'UserController@show');
        Route::delete('/{user_id}', 'UserController@destroy');
    });

    Route::group(['prefix' => 'category'], function () {
        Route::get('/', 'CategoryController@index');
        Route::get('/{category_id}', 'CategoryController@show');
        Route::put('/{category_id}', 'CategoryController@update');
        Route::post('/', 'CategoryController@store');
        Route::delete('/{category_id}', 'CategoryController@destroy');
    });
});

Route::group(['namespace' => 'Web'], function () {
    Route::group(['prefix' => 'user', 'middleware' => 'auth'], function () {
        Route::get('/{user_id}', 'UserController@show');
        Route::put('/{user_id}', 'UserController@update');
    });

    Route::group(['prefix' => 'question'], function () {
        Route::get('/', 'QuestionController@index');
        Route::get('/{question_id}', 'QuestionController@show');
        Route::put('/{question_id}', 'QuestionController@update');
        Route::post('/', 'QuestionController@store');
        Route::delete('/{question_id}', 'QuestionController@destroy');
    });

    Route::group(['prefix' => 'category'], function () {
        Route::get('/', 'CategoryController@index');
    });

    Route::get('history', 'HistoryController@index');
});
