<?php

use App\Http\Controllers\api\JWTAuthController;
use App\Http\Controllers\Api\UrlController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::withoutMiddleware('jwtAuth')->group(function () {
        Route::post('/register', [JWTAuthController::class, 'register'])->name('auth.register');
        Route::post('/login', [JWTAuthController::class, 'login'])->name('auth.login');
        Route::post('/refresh', [JWTAuthController::class, 'refreshToken'])->name('auth.refresh');

    });

    Route::get('/get-user', [JWTAuthController::class, 'getUser'])->name('auth.get-user');
    Route::post('/logout', [JWTAuthController::class, 'logout'])->name('auth.logout');
});

Route::post('shorten', [UrlController::class, 'shorten'])->name('url.shorten');
Route::post('resolve', [UrlController::class, 'resolve'])->name('url.resolve');
Route::put('update-expiry-time', [UrlController::class, 'updateExpiryTime'])->name('url.update-expiry-time');
