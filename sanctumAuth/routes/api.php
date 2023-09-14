<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PasswordResetController;

//Public routes
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::post('/send_reset_password_email', [PasswordResetController::class, 'send_reset_password_email']);
Route::post('/reset/{token}', [PasswordResetController::class, 'reset']);

// Protected routes
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', [UserController::class, 'user']);
    Route::post('/logout', [UserController::class, 'logout']);
    Route::get('/loggedUser', [UserController::class, 'loggedUser']);
    Route::post('/changepassword', [UserController::class, 'change_password']);

});