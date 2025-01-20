<?php

use App\Services\MpesaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request): mixed {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/mpesa-token', function (): mixed {
    return (new MpesaService())->authorize();
});

Route::get('/stk-callback', function (Request $request): mixed {
    Log::info(message: 'STK Callback', context: $request->all());

    return response()->json(data: 'Successfully Logged');
})->name(name: 'stk.callback');
