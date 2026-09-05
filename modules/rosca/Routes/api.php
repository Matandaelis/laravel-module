<?php

use Illuminate\Support\Facades\Route;
use Modules\Rosca\Http\Controllers\Api\RoscaController;
use Modules\Rosca\Http\Controllers\Api\MemberController;
use Modules\Rosca\Http\Controllers\Api\ContributionController;

Route::prefix('api')->middleware(['api','auth:sanctum'])->group(function () {
    Route::apiResource('roscas', RoscaController::class);
    Route::apiResource('rosca-members', MemberController::class);
    Route::apiResource('rosca-contributions', ContributionController::class)->only(['index','store','show']);
});
