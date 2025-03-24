<?php

use App\Http\Controllers\CarbonEmissionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/carbon-emissions/calculate', [CarbonEmissionController::class, 'calculate']);
});

Route::get('/test', function () {
    return response()->json(['message' => 'API is working!']);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
