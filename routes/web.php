<?php

Route::get('/', function () {
    return view('vue');
});

Auth::routes();
