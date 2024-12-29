<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use Illuminate\Http\Request;

class OfferHistoryController extends Controller
{
    public function index(Request $request, Offer $offer)
    {
        $sort_by = $request->input('sort_by', 'created_at');
        $desc = $request->boolean('desc', true);

        $offerHistories = $offer->offerHistories()
            ->with(['operator', 'adminProductAccount'])
            ->orderBy($sort_by, $desc ? 'desc' : 'asc')
            ->paginate(10);

        return inertia('Admin/OfferHistory/Index', compact(
            'offer',
            'offerHistories',
            'sort_by',
            'desc',
        ));
    }
}
