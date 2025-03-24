<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CarbonEmissionController;
use App\Http\Controllers\VerificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Auth routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Email verification routes
Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
    ->name('verification.verify');
Route::post('/email/verification-notification', [VerificationController::class, 'resend'])
    ->name('verification.send');

// Carbon emissions routes
Route::prefix('v1')->middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::post('/carbon-emissions/calculate', [CarbonEmissionController::class, 'calculate']);
});

Route::get('/test', function () {
    return response()->json(['message' => 'API is working!']);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
