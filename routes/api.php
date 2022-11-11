<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/user-profile', [AuthController::class, 'showUserProfile'])
    ->middleware('auth:sanctum');

Route::put('/user-profile', [AuthController::class, 'updateUserProfile'])
    ->middleware('auth:sanctum');

Route::post('/password-change', [AuthController::class, 'changePassword'])
    ->middleware('auth:sanctum');

