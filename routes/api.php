<?php

use App\Http\Controllers\Api\VoicesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Lowel\Telepath\TelegramAppFactoryInterface;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/webhook', function () {
    \Illuminate\Support\Facades\App::make(TelegramAppFactoryInterface::class)
        ->webhook()->start();
});

Route::apiResource('voices', VoicesController::class)->only(['index', 'update']);
