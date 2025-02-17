<?php

use App\Http\Controllers\WebController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/{short_url}',[WebController::class, 'redirect'])->name('url.redirect');