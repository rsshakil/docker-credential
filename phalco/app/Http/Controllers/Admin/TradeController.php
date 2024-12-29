<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OfferType;
use App\Enums\ProblemReportStatus;
use App\Enums\TradeStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Trade\ToCancelDoneRequest;
use App\Http\Requests\Admin\Trade\ToDoneRequest;
use App\Http\Requests\Admin\Trade\ToStopDoneRequest;
use App\Http\Requests\Admin\Trade\ToStopRequest;
use App\Models\Product;
use App\Models\Trade;
use App\Services\AmountCalculationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TradeController extends Controller
{
    public function __construct(
        private readonly AmountCalculationService $amountCalculationService,
    )
    {
    }

    public function index(Request $request)
    {
        $products = Product::all();
        $offer_id = $request->input('offer_id');
        $trade_no = $request->input('trade_no');
        $buyer_no = $request->input('buyer_no');
        $buyer_name = $request->input('buyer_name', '');
        $buyer_email = $request->input('buyer_email', '');
        $seller_no = $request->input('seller_no');
        $seller_name = $request->input('seller_name', '');
        $seller_email = $request->input('seller_email', '');
        $product_id = $request->filled('product_id') ? $request->integer('product_id') : null;
        $offer_type = $request->enum('offer_type', OfferType::class);
        $trade_status = $request->enum('trade_status', TradeStatus::class);
        $buyer_problem_report_status = $request->enum('buyer_problem_report_status', ProblemReportStatus::class);
        $seller_problem_report_status = $request->enum('seller_problem_report_status', ProblemReportStatus::class);

        $sort_by = $request->input('sort_by', 'id');
        $desc = $request->boolean('desc');

        $trades = Trade::with(['product', 'offer', 'buyer', 'seller'])
            ->when(filled($offer_id), fn ($query) => $query->where('offer_id', $offer_id))
            ->when(filled($trade_no), fn ($query) => $query->where('trade_no', $trade_no))
            ->when(filled($buyer_no), fn ($query) => $query->whereHas(
                'buyer',
                fn ($query) => $query->where('user_no', $buyer_no)
            ))
            ->when(filled($buyer_name), fn ($query) => $query->whereHas(
                'buyer',
                fn ($query) => $query->where('name', 'LIKE', '%' . $buyer_name . '%')
            ))
            ->when(filled($buyer_email), fn ($query) => $query->whereHas(
                'buyer',
                fn ($query) => $query->where('email', 'LIKE', '%' . $buyer_email . '%')
            ))
            ->when(filled($seller_no), fn ($query) => $query->whereHas(
                'seller',
                fn ($query) => $query->where('user_no', $seller_no)
            ))
            ->when(filled($seller_name), fn ($query) => $query->whereHas(
                'seller',
                fn ($query) => $query->where('name', 'LIKE', '%' . $seller_name . '%')
            ))
            ->when(filled($seller_email), fn ($query) => $query->whereHas(
                'seller',
                fn ($query) => $query->where('email', 'LIKE', '%' . $seller_email . '%')
            ))
            ->when(filled($product_id), fn ($query) => $query->where('product_id', $product_id))
            ->when(filled($offer_type), fn ($query) => $query->whereHas(
                'offer',
                fn ($query) => $query->where('offer_type', $offer_type)
            ))
            ->when(filled($trade_status), fn ($query) => $query->where('trade_status', $trade_status))
            ->when(filled($buyer_problem_report_status), fn ($query) => $query->where('buyer_problem_report_status', $buyer_problem_report_status))
            ->when(filled($seller_problem_report_status), fn ($query) => $query->where('seller_problem_report_status', $seller_problem_report_status))
            ->orderBy($sort_by, $desc ? 'desc' : 'asc')
            ->paginate(10);

        return inertia('Admin/Trade/Index', compact(
            'trades',
            'products',
            'trade_no',
            'buyer_no',
            'buyer_name',
            'buyer_email',
            'seller_no',
            'seller_name',
            'seller_email',
            'product_id',
            'offer_type',
            'trade_status',
            'buyer_problem_report_status',
            'seller_problem_report_status',
            'sort_by',
            'desc',
        ));
    }

    public function show(Trade $trade)
    {
        $trade->load([
            'offer.currency',
            'sellerPayment' => [
                'paymentMethod',
                'sellerPaymentItems' => [
                    'paymentItem',
                    'paymentOptions',
                ],
            ],
            'product',
            'adminProductAccount' => fn ($query) => $query->withTrashed(),
            'adminProductAccount.adminProductAccountItems' => [
                'productAccountItem' => fn ($query) => $query->withTrashed(),
                'productAccountItemOptions' => fn ($query) => $query->withTrashed(),
            ],
            'seller.userProductAccounts' => fn ($query) => $query
                ->withTrashed()
                ->where('product_id', $trade->offer->product_id),
            'seller.userProductAccounts.userProductAccountItems' => [
                'productAccountItem' => fn ($query) => $query->withTrashed(),
                'productAccountItemOptions' => fn ($query) => $query->withTrashed(),
            ],
            'buyer.userProductAccounts' => fn ($query) => $query
                ->withTrashed()
                ->where('product_id', $trade->offer->product_id),
            'buyer.userProductAccounts.userProductAccountItems' => [
                'productAccountItem' => fn ($query) => $query->withTrashed(),
                'productAccountItemOptions' => fn ($query) => $query->withTrashed(),
            ],
            'latestTradeHistory.adminProductAccount',
            'latestStopTradeHistory',
        ])
        ->append([
            'send_trade_amount',
            'receipt_trade_amount',
            'return_amount',
        ]);

        if (collect([
            TradeStatus::PAID__SEND_PRODUCT,
            TradeStatus::CANCEL__RETURNING,
            TradeStatus::STOP__RETURN,
            TradeStatus::STOP__SEND,
        ])->contains($trade->trade_status)) {
            $sendAdminProductAccounts = $trade->product->adminProductAccounts()->where('is_send', true)->get();
        } else {
            $sendAdminProductAccounts = [];
        }

        return inertia('Admin/Trade/Show', compact(
            'trade',
            'sendAdminProductAccounts',
        ));
    }

    public function toUnpaidWaitPay(Request $request, Trade $trade)
    {
        DB::transaction(function () use ($trade, $request) {
            $trade->update([
                'trade_status' => TradeStatus::UNPAID__WAIT_PAY,
            ]);

            $history = $trade->tradeHistories()->make([
                'result_status' => TradeStatus::UNPAID__WAIT_PAY,
            ]);
            $history->operator()->associate($request->user());
            $history->save();
        });

        return to_route('admin.trades.show', $trade);
    }

    public function toPaidSendProduct(Request $request, Trade $trade)
    {
        DB::transaction(function () use ($trade, $request) {
            $trade->update([
                'trade_status' => TradeStatus::PAID__SEND_PRODUCT,
            ]);

            $history = $trade->tradeHistories()->make([
                'result_status' => TradeStatus::PAID__SEND_PRODUCT,
            ]);
            $history->operator()->associate($request->user());
            $history->save();
        });

        return to_route('admin.trades.show', $trade);
    }

    public function toDone(ToDoneRequest $request, Trade $trade)
    {
        DB::transaction(function () use ($trade, $request) {
            $offer = $trade->offer()->lockForUpdate()->first();
            $offer->amount = $this->amountCalculationService->getOfferAmountAfterTrade($trade);
            $offer->save();

            $trade->update([
                'trade_status' => TradeStatus::DONE,
                'admin_product_account_id' => $request->admin_product_account_id,
            ]);

            $history = $trade->tradeHistories()->make([
                'result_status' => TradeStatus::DONE,
            ]);
            $history->operator()->associate($request->user());
            $history->adminProductAccount()->associate($request->admin_product_account_id);
            $history->save();
        });

        return to_route('admin.trades.show', $trade);
    }

    public function toCancelReturning(Request $request, Trade $trade)
    {
        DB::transaction(function () use ($trade, $request) {
            $trade->update([
                'trade_status' => TradeStatus::CANCEL__RETURNING,
            ]);

            $history = $trade->tradeHistories()->make([
                'result_status' => TradeStatus::CANCEL__RETURNING,
            ]);
            $history->operator()->associate($request->user());
            $history->save();
        });

        return to_route('admin.trades.show', $trade);
    }

    public function toCancelDone(ToCancelDoneRequest $request, Trade $trade)
    {
        DB::transaction(function () use ($trade, $request) {
            $trade->update([
                'trade_status' => TradeStatus::CANCEL__DONE,
                'admin_product_account_id' => $request->admin_product_account_id,
            ]);

            $history = $trade->tradeHistories()->make([
                'result_status' => TradeStatus::CANCEL__DONE,
            ]);
            $history->operator()->associate($request->user());
            $history->adminProductAccount()->associate($request->admin_product_account_id);
            $history->save();
        });

        return to_route('admin.trades.show', $trade);
    }

    public function toStop(ToStopRequest $request, Trade $trade)
    {
        DB::transaction(function () use ($trade, $request) {
            $trade->update([
                'trade_status' => TradeStatus::STOP,
            ]);

            $history = $trade->tradeHistories()->make([
                'result_status' => TradeStatus::STOP,
                'reason' => $request->reason,
            ]);
            $history->operator()->associate($request->user());
            $history->save();
        });

        return to_route('admin.trades.show', $trade);
    }

    public function toStopCancel(Request $request, Trade $trade)
    {
        DB::transaction(function () use ($trade, $request) {
            $status = $trade->tradeHistories()
                ->whereNotIn('result_status', [
                    TradeStatus::STOP,
                    TradeStatus::STOP__RETURN,
                    TradeStatus::STOP__SEND,
                    TradeStatus::STOP__DONE,
                ])
                ->latest()
                ->first()
                ->result_status;

            $trade->update([
                'trade_status' => $status,
            ]);

            $history = $trade->tradeHistories()->make([
                'result_status' => $status,
            ]);
            $history->operator()->associate($request->user());
            $history->save();
        });

        return to_route('admin.trades.show', $trade);
    }

    public function toStopSend(Request $request, Trade $trade)
    {
        DB::transaction(function () use ($trade, $request) {
            $trade->update([
                'trade_status' => TradeStatus::STOP__SEND,
            ]);

            $history = $trade->tradeHistories()->make([
                'result_status' => TradeStatus::STOP__SEND,
            ]);
            $history->operator()->associate($request->user());
            $history->save();
        });

        return to_route('admin.trades.show', $trade);
    }

    public function toStopReturn(Request $request, Trade $trade)
    {
        DB::transaction(function () use ($trade, $request) {
            $trade->update([
                'trade_status' => TradeStatus::STOP__RETURN,
            ]);

            $history = $trade->tradeHistories()->make([
                'result_status' => TradeStatus::STOP__RETURN,
            ]);
            $history->operator()->associate($request->user());
            $history->save();
        });

        return to_route('admin.trades.show', $trade);
    }

    public function toStopDone(ToStopDoneRequest $request, Trade $trade)
    {
        DB::transaction(function () use ($trade, $request) {
            $trade->update([
                'trade_status' => TradeStatus::STOP__DONE,
            ]);

            $history = $trade->tradeHistories()->make([
                'result_status' => TradeStatus::STOP__DONE,
                'admin_product_account_id' => $request->input('admin_product_account_id'),
            ]);
            $history->operator()->associate($request->user());
            $history->save();
        });

        return to_route('admin.trades.show', $trade);
    }
}
