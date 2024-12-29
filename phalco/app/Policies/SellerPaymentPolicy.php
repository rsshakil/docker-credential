<?php

namespace App\Policies;

use App\Models\SellerPayment;
use App\Models\User;

class SellerPaymentPolicy
{
    public function edit(User $user, SellerPayment $sellerPayment): bool
    {
        return $user->id === $sellerPayment->user_id;
    }
}
