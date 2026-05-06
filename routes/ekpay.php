<?php

use FreelancerNishad\Ekpay\Http\Controllers\EkpayController;
use Illuminate\Support\Facades\Route;

$prefix = config('ekpay.route_prefix', 'v1/payments/ekpay');
$middleware = config('ekpay.route_middleware', ['api']);

Route::prefix($prefix)->middleware($middleware)->group(function () {
    Route::post('/initiate', [EkpayController::class, 'initiate']);
    Route::post('/webhook', [EkpayController::class, 'webhook']);

    Route::get('/success', [EkpayController::class, 'success'])->name('ekpay.success');
    Route::get('/fail', [EkpayController::class, 'fail'])->name('ekpay.fail');
    Route::get('/cancel', [EkpayController::class, 'cancel'])->name('ekpay.cancel');

    Route::post('/success', [EkpayController::class, 'success']);
    Route::post('/fail', [EkpayController::class, 'fail']);
    Route::post('/cancel', [EkpayController::class, 'cancel']);

    Route::get('/check-status', [EkpayController::class, 'checkStatus']);
    Route::post('/check-status', [EkpayController::class, 'checkStatus']);
});
