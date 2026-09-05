<?php

use Illuminate\Support\Facades\Route;
use Modules\Rosca\Http\Controllers\Api\HealthController;
use Modules\Rosca\Http\Controllers\Api\RoscaController;
use Modules\Rosca\Http\Controllers\Api\MemberController;
use Modules\Rosca\Http\Controllers\Api\ContributionController;
use Modules\Rosca\Http\Controllers\Api\MpesaController;

// public health endpoint
Route::get('rosca/health', [HealthController::class, 'index']);

Route::prefix('api')->middleware(['api','auth:sanctum'])->group(function () {
    Route::apiResource('roscas', RoscaController::class);
    Route::apiResource('rosca-members', MemberController::class);
    Route::apiResource('rosca-contributions', ContributionController::class)->only(['index','store','show']);

    // MPesa callback endpoint (public; Daraja will POST here)
    Route::post('rosca/mpesa/callback', [MpesaController::class, 'callback']);
});
