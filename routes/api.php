<?php

use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/projects', [ProjectController::class, 'store'])->middleware('auth:sanctum');
Route::patch('/projects/{id}/approve', [ProjectController::class, 'approve'])->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login']);


