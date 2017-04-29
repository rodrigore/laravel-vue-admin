<?php
Route::group(['middleware' => ['jwt.auth']], function () {
    Route::get('/refresh', function () {
    })->middleware('jwt.refresh');

    Route::get('/user', function () {
        return ['data' => auth()->user()];
    });
    Route::get('users', 'UsersController@index');
    Route::post('users', 'UsersController@store');
    Route::put('users/{user}', 'UsersController@update');
    Route::delete('users/{user}', 'UsersController@destroy');
});
