<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserProductAccount;

class UserProductAccountPolicy
{
    public function edit(User $user, UserProductAccount $userProductAccount): bool
    {
        return $user->id === $userProductAccount->user_id;
    }
}
