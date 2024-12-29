<?php

use App\Http\Controllers\Admin\AdministratorController;
use App\Http\Controllers\Admin\AdminProductAccountController;
use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\CurrencyController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ErrorLogController;
use App\Http\Controllers\Admin\KycApplicationController;
use App\Http\Controllers\Admin\KycImageController;
use App\Http\Controllers\Admin\OfferController;
use App\Http\Controllers\Admin\OfferHistoryController;
use App\Http\Controllers\Admin\PaymentMethodController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\TradeController;
use App\Http\Controllers\Admin\TradeHistoryController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/admin/dashboard')->name('root');

Route::middleware('guest:admin')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware('auth:admin')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::get('dashboard', DashboardController::class)->name('dashboard');

    Route::get('offers', [OfferController::class, 'index'])->name('offers.index');
    Route::get('offers/{offer}', [OfferController::class, 'show'])->name('offers.show');
    Route::post('offers/{offer}/to_publishing_active', [OfferController::class, 'toPublishingActive'])->name('offers.to_publishing_active');
    Route::post('offers/{offer}/to_reject_checking', [OfferController::class, 'toRejectChecking'])->name('offers.to_reject_checking');
    Route::post('offers/{offer}/to_reject_returning', [OfferController::class, 'toRejectReturning'])->name('offers.to_reject_returning');
    Route::post('offers/{offer}/to_reject_done', [OfferController::class, 'toRejectDone'])->name('offers.to_reject_done');
    Route::post('offers/{offer}/to_cancel_returning', [OfferController::class, 'toCancelReturning'])->name('offers.to_cancel_returning');
    Route::post('offers/{offer}/to_cancel_done', [OfferController::class, 'toCancelDone'])->name('offers.to_cancel_done');
    Route::post('offers/{offer}/to_stop', [OfferController::class, 'toStop'])->name('offers.to_stop');
    Route::post('offers/{offer}/to_stop_cancel', [OfferController::class, 'toStopCancel'])->name('offers.to_stop_cancel');

    Route::get('offers/{offer}/offer_histories', [OfferHistoryController::class, 'index'])->name('offer_histories.index');

    Route::get('trades', [TradeController::class, 'index'])->name('trades.index');
    Route::get('trades/{trade}', [TradeController::class, 'show'])->name('trades.show');
    Route::post('trades/{trade}/to_unpaid_wait_pay', [TradeController::class, 'toUnpaidWaitPay'])->name('trades.to_unpaid_wait_pay');
    Route::post('trades/{trade}/to_paid_send_product', [TradeController::class, 'toPaidSendProduct'])->name('trades.to_paid_send_product');
    Route::post('trades/{trade}/to_done', [TradeController::class, 'toDone'])->name('trades.to_done');
    Route::post('trades/{trade}/to_cancel_returning', [TradeController::class, 'toCancelReturning'])->name('trades.to_cancel_returning');
    Route::post('trades/{trade}/to_cancel_done', [TradeController::class, 'toCancelDone'])->name('trades.to_cancel_done');
    Route::post('trades/{trade}/to_stop', [TradeController::class, 'toStop'])->name('trades.to_stop');
    Route::post('trades/{trade}/to_stop_cancel', [TradeController::class, 'toStopCancel'])->name('trades.to_stop_cancel');
    Route::post('trades/{trade}/to_stop_send', [TradeController::class, 'toStopSend'])->name('trades.to_stop_send');
    Route::post('trades/{trade}/to_stop_return', [TradeController::class, 'toStopReturn'])->name('trades.to_stop_return');
    Route::post('trades/{trade}/to_stop_done', [TradeController::class, 'toStopDone'])->name('trades.to_stop_done');

    Route::get('trades/{trade}/trade_histories', [TradeHistoryController::class, 'index'])->name('trade_histories.index');

    Route::get('products', [ProductController::class, 'index'])->name('products.index');
    Route::get('products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('products', [ProductController::class, 'store'])->name('products.store');
    Route::get('products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('products/{product}', [ProductController::class, 'update'])->name('products.update');

    Route::get('admin_product_accounts', [AdminProductAccountController::class, 'index'])->name('admin_product_accounts.index');
    Route::get('admin_product_accounts/create', [AdminProductAccountController::class, 'create'])->name('admin_product_accounts.create');
    Route::post('admin_product_accounts', [AdminProductAccountController::class, 'store'])->name('admin_product_accounts.store');
    Route::get('admin_product_accounts/{admin_product_account}/edit', [AdminProductAccountController::class, 'edit'])->name('admin_product_accounts.edit');
    Route::put('admin_product_accounts/{admin_product_account}', [AdminProductAccountController::class, 'update'])->name('admin_product_accounts.update');

    /** 決済方法管理 */
    Route::get('payment_methods', [PaymentMethodController::class, 'index'])->name('payment_methods.index');
    Route::get('payment_methods/create', [PaymentMethodController::class, 'create'])->name('payment_methods.create');
    Route::post('payment_methods', [PaymentMethodController::class, 'store'])->name('payment_methods.store');
    Route::get('payment_methods/{payment_method}/edit', [PaymentMethodController::class, 'edit'])->name('payment_methods.edit');
    Route::put('payment_methods/{payment_method}', [PaymentMethodController::class, 'update'])->name('payment_methods.update');

    /** 通貨管理 */
    Route::get('currency', [CurrencyController::class, 'index'])->name('currency.index');
    Route::get('currency/create', [CurrencyController::class, 'create'])->name('currency.create');
    Route::post('currency', [CurrencyController::class, 'store'])->name('currency.store');
    Route::get('currency/{currency}/edit', [CurrencyController::class, 'edit'])->name('currency.edit');
    Route::put('currency/{currency}', [CurrencyController::class, 'update'])->name('currency.update');

    Route::get('errorlog', [ErrorLogController::class, 'index'])->name('errorlog.index');

    Route::get('administrators', [AdministratorController::class, 'index'])->name('administrators.index');
    Route::get('administrators/create', [AdministratorController::class, 'create'])->name('administrators.create');
    Route::post('administrators', [AdministratorController::class, 'store'])->name('administrators.store');
    Route::get('administrators/{administrator}/edit', [AdministratorController::class, 'edit'])->name('administrators.edit');
    Route::put('administrators/{administrator}', [AdministratorController::class, 'update'])->name('administrators.update');

    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');

    Route::get('users/{user}/kyc_application', [KycApplicationController::class, 'show'])->name('kyc_applications.show');
    Route::post('users/{user}/kyc_application/approve', [KycApplicationController::class, 'approve'])->name('kyc_applications.approve');
    Route::post('users/{user}/kyc_application/reject', [KycApplicationController::class, 'reject'])->name('kyc_applications.reject');
    Route::get('kyc_images/{kyc_image}/get_image', [KycImageController::class, 'getImage'])->name('kyc_images.get_image');
    Route::delete('kyc_images/{kyc_image}', [KycImageController::class, 'delete'])->name('kyc_images.delete');

    /** 通貨管理 */
    Route::get('announcement', [AnnouncementController::class, 'index'])->name('announcement.index');
    Route::get('announcement/create', [AnnouncementController::class, 'create'])->name('announcement.create');
    Route::post('announcement', [AnnouncementController::class, 'store'])->name('announcement.store');
    Route::get('announcement/{announcement}/edit', [AnnouncementController::class, 'edit'])->name('announcement.edit');
    Route::put('announcement/{announcement}', [AnnouncementController::class, 'update'])->name('announcement.update');

});
