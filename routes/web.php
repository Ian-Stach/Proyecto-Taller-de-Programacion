<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('Principal');
});

Route::get('/Principal', function () {
    return view('Principal');
});
