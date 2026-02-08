<?php

use App\Http\Controllers\Api\MediaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('/media')->group(function () {
        Route::get('/', [MediaController::class, 'index']);

        Route::get('/empty/', [MediaController::class, 'empty']);

        Route::post('/media/unique/', [MediaController::class, 'unique']);

        Route::put('/{tg_file}/text/', [MediaController::class, 'update']);
    });
});
