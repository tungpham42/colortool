<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ColorController;

Route::prefix('api/colors')->group(function () {
    // Color lookup
    Route::get('/lookup', [ColorController::class, 'lookup']);

    // Color picker
    Route::get('/picker', [ColorController::class, 'picker']);

    // Color converter
    Route::post('/convert', [ColorController::class, 'convert']);

    // Color mixer
    Route::post('/mix', [ColorController::class, 'mix']);

    // Extract from image
    Route::post('/extract', [ColorController::class, 'extractFromImage']);

    // Find similar colors
    Route::get('/similar', [ColorController::class, 'similar']);

    // Color database operations
    Route::apiResource('database', ColorController::class);

    // Color harmonies
    Route::get('/shades-tints', [ColorController::class, 'shadesAndTints']);
    Route::get('/monochromatic', [ColorController::class, 'monochromatic']);
    Route::get('/analogous', [ColorController::class, 'analogous']);
    Route::get('/complementary', [ColorController::class, 'complementary']);
    Route::get('/triadic', [ColorController::class, 'triadic']);
    Route::get('/split-complementary', [ColorController::class, 'splitComplementary']);
    Route::get('/tetradic', [ColorController::class, 'tetradic']);
    Route::get('/square', [ColorController::class, 'square']);
    Route::get('/harmonies', [ColorController::class, 'harmonies']);

    // Color contrast
    Route::get('/contrast-ratio', [ColorController::class, 'contrastRatio']);
});
