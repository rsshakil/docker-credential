<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Trade;
use Illuminate\Http\Request;

class TradeHistoryController extends Controller
{
    public function index(Request $request, Trade $trade)
    {
        $sort_by = $request->input('sort_by', 'created_at');
        $desc = $request->boolean('desc', true);

        $tradeHistories = $trade->tradeHistories()
            ->with(['operator', 'adminProductAccount'])
            ->orderBy($sort_by, $desc ? 'desc' : 'asc')
            ->paginate(10);

        return inertia('Admin/TradeHistory/Index', compact(
            'trade',
            'tradeHistories',
            'sort_by',
            'desc',
        ));
    }
}
