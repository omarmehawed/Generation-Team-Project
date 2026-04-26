<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CreativaController;

Route::prefix('creativa')->group(function () {
    Route::get('/', [CreativaController::class, 'index'])->name('creativa.index');
    Route::get('/draw', [CreativaController::class, 'draw'])->name('creativa.draw');
});

Route::prefix('api/creativa')->group(function () {
    Route::get('/submissions/pool', [CreativaController::class, 'getPool']);
    Route::get('/submissions', [CreativaController::class, 'getAll']);
    Route::post('/submit', [CreativaController::class, 'submit']);
    Route::post('/submissions/mark-used/{id}', [CreativaController::class, 'markUsed']);
});
