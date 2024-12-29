<?php

namespace App\Policies;

use App\Models\BuyerPayment;
use App\Models\User;

class BuyerPaymentPolicy
{
    public function edit(User $user, BuyerPayment $buyerPayment): bool
    {
        return $user->id === $buyerPayment->user_id;
    }
}
