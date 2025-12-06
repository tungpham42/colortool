<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\ColorController;

Route::get('/', function () {
    return view('pages.home');
})->name('home');

Route::get('/color-picker', function () {
    return view('pages.color-picker');
})->name('color-picker');

Route::get('/color-mixer', function () {
    return view('pages.color-mixer');
})->name('color-mixer');

Route::get('/image-extractor', function () {
    return view('pages.image-extractor');
})->name('image-extractor');

Route::get('/color-lookup', function () {
    return view('pages.color-lookup');
})->name('color-lookup');

Route::get('/{hex}', [ColorController::class, 'show'])
    ->where('hex', '[0-9A-Fa-f]{6}')
    ->name('color.details');

// API Routes for AJAX
Route::prefix('api')->group(function () {
    Route::post('/convert-color', [ColorController::class, 'convert']);
    Route::post('/extract-colors', [ColorController::class, 'extract'])->name('api.extract-colors');
    Route::post('/save-palette', [ColorController::class, 'savePalette']);
    Route::get('/search-colors', [ColorController::class, 'search'])->name('api.search-colors');
});

Route::get('/test-redis', function () {
    $key = 'redis_test_key';

    // Put to cache for 60 seconds
    Cache::put($key, 'Redis is working!', 60);

    // Retrieve value
    $value = Cache::get($key);

    return response()->json([
        'value' => $value,
        'driver' => config('cache.default'),
        'redis_connection' => config('database.redis.default')
    ]);
});
