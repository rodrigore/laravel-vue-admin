<?php

Route::group(['middleware' => ['jwt.auth']], function () {
    Route::get('/refresh', function () {
    })->middleware('jwt.refresh');

    Route::get('/user', function () {
        return ['data' => auth()->user()];
    });
});

Route::get('users', 'UsersController@index');
Route::post('users', 'UsersController@store');
