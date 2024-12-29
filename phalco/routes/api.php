<?php

use App\Http\Controllers\Api\OfferController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/offer/get_product_rate', [OfferController::class, 'getProductRate'])->name('get_product_rate');
    Route::get('/offer/get_display_amount_buy', [OfferController::class, 'getDisplayAmountBuy'])->name('get_display_amount_buy');
    Route::get('/offer/get_display_amount_sell', [OfferController::class, 'getDisplayAmountSell'])->name('get_display_amount_sell');
    Route::get('/offer/get_offer_remaining_time/{offer}', [OfferController::class, 'getOfferRemainingTime'])->name('get_offer_remaining_time');
});
