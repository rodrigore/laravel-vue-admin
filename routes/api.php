<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('users', function() {
    return \App\User::
        when(request()->sortBy, function ($query) {
            list($column, $order)  = explode(',', request()->sortBy);
            $query->orderBy($column, $order);
        })
        ->paginate();
});

Route::post('users', function() {
    \App\User::create(request()->params);
});
