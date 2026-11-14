<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CampagneController;
use App\Http\Controllers\Api\DistributionController;
use App\Http\Controllers\Api\IntrantController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/intrants', [IntrantController::class, 'index']);
    Route::get('/intrants/{intrant}', [IntrantController::class, 'show']);

    Route::get('/campagnes', [CampagneController::class, 'index']);
    Route::get('/campagnes/{campagne}', [CampagneController::class, 'show']);

    Route::post('/distributions', [DistributionController::class, 'store']);
});
