<?php

use App\Http\Controllers\Auth\Login;
use App\Http\Controllers\Auth\Logout;
use App\Http\Controllers\Auth\Register;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DiaryController;
use App\Http\Controllers\EntryDetailController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function() {
    Route::get('/', [DiaryController::class,'index']);
    Route::post('/entry', [DiaryController::class, 'store']);
    Route::delete('/entry/{entry}', [DiaryController::class, 'destroy']);
    
    Route::get('/entry/{entry}', [DiaryController::class, 'detail']);
    Route::post('/entry/{entry}/edit', [DiaryController::class,'update']);

    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::middleware('guest')->group(function() {
    Route::view('/login', 'login')->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::view('/register', 'register')->name('register');
    Route::post('/register', [AuthController::class,'register']);
});