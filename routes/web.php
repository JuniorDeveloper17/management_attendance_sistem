<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/linkstorage', function () {
    Artisan::call('storage:link');
});

//php artisan serve --host=192.168.181.186 --port=8006
