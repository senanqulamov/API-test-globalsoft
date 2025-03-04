<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;

Route::group(['middleware' => ['jwt.auth', 'api.log']], function () {

    Route::post('/orders', [OrderController::class, 'create']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::put('/orders/{id}', [OrderController::class, 'update']);
    Route::delete('/orders/{id}', [OrderController::class, 'delete']);
    Route::get('/orders', [OrderController::class, 'index']);
});
