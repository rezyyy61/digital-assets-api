<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DigitalAssetController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/digital-assets', [DigitalAssetController::class, 'store']);
    Route::get('/digital-assets', [DigitalAssetController::class, 'index']);
    Route::post('/digital-assets/{id}/mint', [DigitalAssetController::class, 'mint']);
});
