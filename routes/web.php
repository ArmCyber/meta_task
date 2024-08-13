<?php

use App\Http\Controllers\MediaLibraryController;
use Illuminate\Support\Facades\Route;

Route::get('/', [MediaLibraryController::class, 'index']);
Route::get('/media/{id}/offset', [MediaLibraryController::class, 'getAssetOffset']);
Route::post('/media/upload', [MediaLibraryController::class, 'upload']);
