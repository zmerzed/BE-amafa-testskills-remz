<?php

use Boilerplate\Media\Http\Controllers\V1\MediaController;
use Illuminate\Support\Facades\Route;

Route::get('/media/{media}/responsive', [MediaController::class, 'imageFactory'])
    ->name('responsive');
Route::apiResource('media', MediaController::class)->only(['store', 'show', 'destroy'])
    ->parameter('media', 'media');
