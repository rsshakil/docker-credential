<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ErrorLog;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;

class ErrorLogController extends Controller
{
    public function __construct()
    {
        Gate::authorize('any', ErrorLog::class);
    }

    public function index(Request $request)
    {
        $request->validate(
            [
                'date' => 'date_format:Y/m/d',
            ],
            [
                'date' => '不正な日付です。'
            ]
        );

        $date = $request->string('date', Carbon::now()->format('Y/m/d'));
        $errorLogs = ErrorLog::query()
            ->whereDate('date', '=', $date)
            ->get();

        return inertia('Admin/ErrorLog/Index', compact(
            'errorLogs',
        ));
    }
}
