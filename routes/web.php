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

    Route::group(['prefix'  =>  'question'], function () {
        Route::get('/index', 'QuestionController@index');
        Route::get('/{question_id}/show', 'QuestionController@show');
        Route::post('/create', 'QuestionController@store');
        Route::put('/{question_id}/edit', 'QuestionController@update');
        Route::delete('/{question_id}', 'QuestionController@destroy');

        //Answer
        Route::get('/{question_id}/show', 'QuestionController@show');
        Route::post('/create', 'AnswerController@store');
        Route::put('/{question_id}/edit', 'QuestionController@update');
        Route::delete('/{question_id}', 'AnswerController@destroy');
    });
});

Route::group(['namespace' => 'Web'], function () {
    Route::group(['prefix' => 'user', 'middleware' => 'auth'], function () {
        Route::get('/{user_id}', 'UserController@show');
        Route::put('/{user_id}', 'UserController@update');
    });

    Route::group(['prefix' => 'question'], function () {
        Route::get('/index', 'QuestionController@index');
        Route::get('/{question_id}/show', 'QuestionController@show');
        Route::put('/{question_id}/edit', 'QuestionController@update');
        Route::post('/create', 'QuestionController@store');
        Route::delete('/{question_id}', 'QuestionController@destroy');

        //Answer
        Route::get('/{question_id}/show', 'AnswerController@show');
        Route::put('/{question_id}/edit', 'AnswerController@update');
        Route::post('/create', 'AnswerController@store');
        Route::delete('/{question_id}', 'AnswerController@destroy');
        Route::post('/{question_id}/show', 'AnswerController@reply');
    });

    Route::group(['prefix' => 'category'], function () {
        Route::get('/', 'CategoryController@index');
    });

    Route::get('history', 'HistoryController@index');
});
