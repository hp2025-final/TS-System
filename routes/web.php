<?php

use Illuminate\Support\Facades\Route;

// Main POS application route
Route::get('/', function () {
    return view('app');
});

// API documentation route
Route::get('/docs', function () {
    return view('api-docs');
});

// Serve the Vue SPA for all routes (Vue Router will handle these)
Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');
