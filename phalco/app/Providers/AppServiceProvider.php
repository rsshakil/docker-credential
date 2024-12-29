<?php

namespace App\Providers;

use App\Models\BuyerPayment;
use App\Models\SellerPayment;
use App\Models\UserProductAccount;
use App\Policies\UserEditRequestPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
