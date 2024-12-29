<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\Administrator;

class ErrorLogPolicy
{
    public function any(Administrator $administrator)
    {
        return $administrator->role === Role::SV;
    }
}
