<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', 'dashboard');

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware('auth')->prefix('/')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::get('dashboard', DashboardController::class)->name('dashboard');

    Route::get('offers/create', [OfferController::class, 'create'])->name('offers.create');
    Route::post('offers', [OfferController::class, 'store'])->name('offers.store');
    Route::get('offers/{offer:offer_no}/edit', [OfferController::class, 'edit'])->name('offers.edit');
    Route::put('offers/{offer:offer_no}/update', [OfferController::class, 'update'])->name('offers.update');
    Route::get('offers/{offer:offer_no}/detail', [OfferController::class, 'detail'])->name('offers.detail');
    Route::post('offers/{offer:offer_no}/cancel', [OfferController::class, 'cancel'])->name('offers.cancel');
    Route::post('offers/{offer:offer_no}/send_product', [OfferController::class, 'sendProduct'])->name('offers.send_product');
    Route::post('offers/{offer:offer_no}/pause', [OfferController::class, 'pause'])->name('offers.pause');
    Route::post('offers/{offer:offer_no}/active', [OfferController::class, 'active'])->name('offers.active');

    Route::get('myprofile', [UserController::class, 'showMyProfile'])->name('profile.show_my_profile');
    Route::get('profile/{user}', [UserController::class, 'showProfile'])->name('profile.show_profile');
    Route::put('myprofile', [UserController::class, 'update'])->name('profile.update');
    Route::get('myprofile/kyc_application', [UserController::class, 'createKycApplication'])->name('kyc_application.create');
    Route::post('myprofile/kyc_application', [UserController::class, 'storeKycApplication'])->name('kyc_application.store');
    Route::get('myprofile/password', [UserController::class, 'editPassword'])->name('password.edit');
    Route::put('myprofile/password', [UserController::class, 'updatePassword'])->name('password.update');
    Route::get('myprofile/email', [UserController::class, 'editEmail'])->name('email.edit');
    Route::put('myprofile/email', [UserController::class, 'updateEmail'])->name('email.update');

    Route::get('myprofile/buyer_payment', [UserController::class, 'showBuyerPayment'])->name('buyer_payment.show');
    Route::post('myprofile/buyer_payment', [UserController::class, 'storeBuyerPayment'])->name('buyer_payment.store');
    Route::delete('myprofile/buyer_payment/{buyerPayment}', [UserController::class, 'deleteBuyerPayment'])->name('buyer_payment.delete');
    Route::put('myprofile/buyer_payment/{buyerPayment}/toggleActive', [UserController::class, 'activeBuyerPaymentToggle'])->name('buyer_payment.toggle_active');

    Route::get('myprofile/seller_payment', [UserController::class, 'showSellerPayment'])->name('seller_payment.show');
    Route::get('myprofile/seller_payment/create', [UserController::class, 'createSellerPayment'])->name('seller_payment.create');
    Route::post('myprofile/seller_payment', [UserController::class, 'storeSellerPayment'])->name('seller_payment.store');
    Route::get('myprofile/seller_payment/{sellerPayment}/edit', [UserController::class, 'editSellerPayment'])->name('seller_payment.edit');
    Route::put('myprofile/seller_payment/{sellerPayment}', [UserController::class, 'updateSellerPayment'])->name('seller_payment.update');
    Route::delete('myprofile/seller_payment/{sellerPayment}', [UserController::class, 'deleteSellerPayment'])->name('seller_payment.delete');
    Route::put('myprofile/seller_payment/{sellerPayment}/toggleActive', [UserController::class, 'activeSellerPaymentToggle'])->name('seller_payment.toggle_active');

    Route::get('myprofile/user_product_account', [UserController::class, 'showProductAccount'])->name('user_product_account.show');
    Route::get('myprofile/user_product_account/create', [UserController::class, 'createProductAccount'])->name('user_product_account.create');
    Route::post('myprofile/user_product_account', [UserController::class, 'storeProductAccount'])->name('user_product_account.store');
    Route::get('myprofile/user_product_account/{userProductAccount}/edit', [UserController::class, 'editProductAccount'])->name('user_product_account.edit');
    Route::put('myprofile/user_product_account/{userProductAccount}', [UserController::class, 'updateProductAccount'])->name('user_product_account.update');
    Route::delete('myprofile/user_product_account/{userProductAccount}', [UserController::class, 'deleteProductAccount'])->name('user_product_account.delete');
});

Route::get('offers', [OfferController::class, 'index'])->name('offers.index');
