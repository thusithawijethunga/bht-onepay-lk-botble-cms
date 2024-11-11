<?php

use Illuminate\Support\Facades\Route;
use ByHeartLK\OnepayLk\Http\Controllers\OnepayLkController;

Route::post('checkout/payment/onepay/payment-success', [OnepayLkController::class, 'callback'])
->name('payments.onepaylk.callback');

Route::prefix('payment/onepaylk')
    ->name('payments.onepaylk.')
    ->group(function (): void {
        Route::middleware(['web', 'core'])->group(function (): void {
            Route::get('success', [OnepayLkController::class, 'getSuccess'])->name('success');
            Route::get('cancel', [OnepayLkController::class, 'getCancel'])->name('cancel');
        });
    });
