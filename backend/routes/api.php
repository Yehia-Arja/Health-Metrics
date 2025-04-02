<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

Route::group(['prefix' => 'v0.1'], function () {
    Route::group(['prefix' => 'guest'], function () {
        Route::post('/signup', [AuthController::class, 'signup']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::get('/dashboard', [DashboardController::class, 'index']);
        Route::get('/activity', [DashboardController::class, 'getActivity']);
        Route::post('/upload',[DashboardController::class, 'store']);
    });
       
});