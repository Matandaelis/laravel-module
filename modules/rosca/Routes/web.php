<?php

use Illuminate\Support\Facades\Route;
use Modules\Rosca\Http\Controllers\Web\RoscaController;

Route::middleware('web')->group(function () {
    Route::get('roscas', [RoscaController::class, 'index'])->name('rosca.index');
});
