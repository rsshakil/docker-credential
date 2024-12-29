<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OfferStatus;
use App\Enums\OfferType;
use App\Enums\ProblemReportStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Offer\ToCancelDoneRequest;
use App\Http\Requests\Admin\Offer\ToStopRequest;
use App\Models\Offer;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OfferController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::all();
        $offer_no = $request->input('offer_no');
        $user_no = $request->input('user_no');
        $user_email = $request->input('user_email', '');
        $user_name = $request->input('user_name', '');
        $product_id = $request->filled('product_id') ? $request->integer('product_id') : null;
        $offer_type = $request->enum('offer_type', OfferType::class);
        $offer_status = $request->enum('offer_status', OfferStatus::class);
        $problem_report_status = $request->enum('problem_report_status', ProblemReportStatus::class);
        $amount_from = $request->input('amount_from');
        $amount_to = $request->input('amount_to');
        $requested_at_from = $request->date('requested_at_from');
        $requested_at_to = $request->date('requested_at_to');
        $sort_by = $request->input('sort_by', 'id');
        $desc = $request->boolean('desc');

        $offers = Offer::with(['user', 'product'])
            ->when(filled($offer_no), fn ($query) => $query->where('offer_no', $offer_no))
            ->when(filled($user_no), fn ($query) => $query->whereHas(
                'user',
                fn ($query) => $query->where('user_no', $user_no)
            ))
            ->when(filled($user_email), fn ($query) => $query->whereHas(
                'user',
                fn ($query) => $query->where('email', 'like', '%' . $user_email . '%')
            ))
            ->when(filled($user_name), fn ($query) => $query->whereHas(
                'user',
                fn ($query) => $query->where('name', 'like', '%' . $user_name . '%')
            ))
            ->when(filled($product_id), fn ($query) => $query->where('product_id', $product_id))
            ->when(filled($offer_type), fn ($query) => $query->where('offer_type', $offer_type))
            ->when(filled($offer_status), fn ($query) => $query->where('offer_status', $offer_status))
            ->when(filled($problem_report_status), fn ($query) => $query->where('problem_report_status', $problem_report_status))
            ->when(filled($amount_from), fn ($query) => $query->where('amount', '>=', $amount_from))
            ->when(filled($amount_to), fn ($query) => $query->where('amount', '<=', $amount_to))
            ->when(filled($requested_at_from), fn ($query) => $query->where('requested_at', '>=', $requested_at_from))
            ->when(filled($requested_at_to), fn ($query) => $query->where('requested_at', '<=', $requested_at_to))
            ->orderBy($sort_by, $desc ? 'desc' : 'asc')
            ->paginate(10);

        return inertia('Admin/Offer/Index', compact(
            'offers',
            'products',
            'offer_no',
            'user_no',
            'user_email',
            'user_name',
            'product_id',
            'offer_type',
            'offer_status',
            'problem_report_status',
            'amount_from',
            'amount_to',
            'requested_at_from',
            'requested_at_to',
            'sort_by',
            'desc',
        ));
    }

    public function show(Offer $offer)
    {
        $offer
            ->load([
                'user' => [
                    'sellerPayments.paymentMethod',
                    'buyerPayments.paymentMethod',
                    'userProductAccounts' => fn ($query) => $query
                        ->withTrashed()
                        ->where('product_id', $offer->product_id),
                    'userProductAccounts.userProductAccountItems' => [
                        'productAccountItem' => fn ($query) => $query->withTrashed(),
                        'productAccountItemOptions' => fn ($query) => $query->withTrashed(),
                    ],
                ],
                'product',
                'currency',
                'latestOfferHistory.adminProductAccount',
                'adminProductAccount' => fn ($query) => $query->withTrashed(),
                'adminProductAccount.adminProductAccountItems' => [
                    'productAccountItem' => fn ($query) => $query->withTrashed(),
                    'productAccountItemOptions' => fn ($query) => $query->withTrashed(),
                ],
            ])
            ->append(['display_amount', 'return_amount']);

        if ($offer->offer_status === OfferStatus::CANCEL__RETURNING) {
            $sendAdminProductAccounts = $offer->product->adminProductAccounts()->where('is_send', true)->get();
        } else {
            $sendAdminProductAccounts = [];
        }

        return inertia('Admin/Offer/Show', compact(
            'offer',
            'sendAdminProductAccounts',
        ));
    }

    public function toPublishingActive(Request $request, Offer $offer)
    {
        DB::transaction(function () use ($offer, $request) {
            $offer->update([
                'offer_status' => OfferStatus::PUBLISHING__ACTIVE,
                'publish_date' => now(),
            ]);

            $history = $offer->offerHistories()->make([
                'result_status' => OfferStatus::PUBLISHING__ACTIVE,
            ]);
            $history->operator()->associate($request->user());
            $history->adminProductAccount()->associate($offer->adminProductAccount);
            $history->save();
        });

        return to_route('admin.offers.show', $offer);
    }

    public function toRejectChecking(Request $request, Offer $offer)
    {
        DB::transaction(function () use ($offer, $request) {
            $offer->update([
                'offer_status' => OfferStatus::REJECT__CHECKING,
            ]);

            $history = $offer->offerHistories()->make([
                'result_status' => OfferStatus::REJECT__CHECKING,
            ]);
            $history->operator()->associate($request->user());
            $history->save();
        });

        return to_route('admin.offers.show', $offer);
    }

    public function toRejectReturning(Request $request, Offer $offer)
    {
        DB::transaction(function () use ($offer, $request) {
            $offer->update([
                'offer_status' => OfferStatus::REJECT__RETURNING,
            ]);

            $history = $offer->offerHistories()->make([
                'result_status' => OfferStatus::REJECT__RETURNING,
            ]);
            $history->operator()->associate($request->user());
            $history->save();
        });

        return to_route('admin.offers.show', $offer);
    }

    public function toRejectDone(Request $request, Offer $offer)
    {
        DB::transaction(function () use ($offer, $request) {
            $offer->update([
                'offer_status' => OfferStatus::REJECT__DONE,
            ]);

            $history = $offer->offerHistories()->make([
                'result_status' => OfferStatus::REJECT__DONE,
            ]);
            $history->operator()->associate($request->user());
            $history->save();
        });

        return to_route('admin.offers.show', $offer);
    }

    public function toCancelReturning(Request $request, Offer $offer)
    {
        DB::transaction(function () use ($offer, $request) {
            $offer->update([
                'offer_status' => OfferStatus::CANCEL__RETURNING,
            ]);

            $history = $offer->offerHistories()->make([
                'result_status' => OfferStatus::CANCEL__RETURNING,
            ]);
            $history->operator()->associate($request->user());
            $history->save();
        });

        return to_route('admin.offers.show', $offer);
    }

    public function toCancelDone(ToCancelDoneRequest $request, Offer $offer)
    {
        DB::transaction(function () use ($offer, $request) {
            $offer->update([
                'offer_status' => OfferStatus::CANCEL__DONE,
            ]);

            $history = $offer->offerHistories()->make([
                'result_status' => OfferStatus::CANCEL__DONE,
                'admin_product_account_id' => $request->admin_product_account_id,
            ]);
            $history->operator()->associate($request->user());
            $history->adminProductAccount()->associate($request->admin_product_account_id);
            $history->save();
        });

        return to_route('admin.offers.show', $offer);
    }

    public function toStop(ToStopRequest $request, Offer $offer)
    {
        DB::transaction(function () use ($offer, $request) {
            $offer->update([
                'offer_status' => OfferStatus::STOP,
            ]);

            $history = $offer->offerHistories()->make([
                'result_status' => OfferStatus::STOP,
                'reason' => $request->reason,
            ]);
            $history->operator()->associate($request->user());
            $history->save();
        });

        return to_route('admin.offers.show', $offer);
    }

    public function toStopCancel(Request $request, Offer $offer)
    {
        DB::transaction(function () use ($offer, $request) {
            $status = $offer->prevOfferHistory->result_status;

            $offer->update([
                'offer_status' => $status,
            ]);

            $history = $offer->offerHistories()->make([
                'result_status' => $status,
            ]);
            $history->operator()->associate($request->user());
            $history->save();
        });

        return to_route('admin.offers.show', $offer);
    }
}
