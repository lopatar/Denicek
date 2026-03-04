<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DiaryController;
use App\Http\Controllers\FileController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function() {
    //Main route redirects to /page/1 so you automatically see the latest entries
    Route::redirect('/', '/page/1')->name('home');
    Route::get('/page/{page}', [DiaryController::class,'page']);

    Route::post('/entry', [DiaryController::class, 'store']);
    Route::delete('/entry/{entry}', [DiaryController::class, 'destroy']);
    
    Route::get('/entry/{entry}', [DiaryController::class, 'detail']);
    Route::post('/entry/{entry}/edit', [DiaryController::class,'update']);

    Route::get('/file/{file}', [FileController::class,'file']);
    Route::delete('/file/{file}', [FileController::class,'destroy']);

    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::middleware('guest')->group(function() {
    Route::view('/login', 'login')->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::view('/register', 'register')->name('register');
    Route::post('/register', [AuthController::class,'register']);
});