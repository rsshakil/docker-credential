<?php

namespace App\Http\Controllers\Admin;

use App\Enums\KycStatus;
use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $user_no = $request->input('user_no');
        $email = $request->input('email', '');
        $name = $request->input('name');
        $user_status = $request->enum('user_status', UserStatus::class);
        $kyc_status = $request->enum('kyc_status', KycStatus::class);
        $sort_by = $request->input('sort_by', 'id');
        $desc = $request->boolean('desc');

        $users = User::query()
            ->when(filled($user_no), fn ($query) => $query->where('user_no', $user_no))
            ->when(filled($email), fn ($query) => $query->where('email', 'LIKE', "%{$email}%"))
            ->when(filled($name), fn ($query) => $query->where('name', 'LIKE', "%{$name}%"))
            ->when(filled($user_status), fn ($query) => $query->where('user_status', $user_status))
            ->when(filled($kyc_status), fn ($query) => $query->where('kyc_status', $kyc_status))
            ->orderBy($sort_by, $desc ? 'desc' : 'asc')
            ->paginate(10);

        return inertia('Admin/User/Index', compact(
            'users',
            'user_no',
            'email',
            'name',
            'user_status',
            'kyc_status',
            'sort_by',
            'desc',
        ));
    }

    public function show(Request $request, User $user)
    {
        return inertia('Admin/User/Show', compact(
            'user',
        ));
    }
}
