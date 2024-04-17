<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::group(['controller' => AuthController::class], function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::get('logout', 'logout');
        Route::get('user', 'user');
    });
});
