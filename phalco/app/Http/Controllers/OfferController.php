<?php

namespace App\Http\Controllers;

use App\Enums\OfferStatus;
use App\Enums\OfferType;
use App\Enums\ProblemReportStatus;
use App\Http\Requests\Offer\StoreOfferRequest;
use App\Http\Requests\Offer\UpdateOfferRequest;
use App\Jobs\JobsOfferSendingProductTimeout;
use App\Models\Currency;
use App\Models\Offer;
use App\Models\Product;
use App\Models\UserProductAccount;
use App\Services\NumberingService;
use App\Services\OfferService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OfferController extends Controller
{
    public function __construct(
        readonly private OfferService $offerService,
        readonly private NumberingService $numberingService
    ) {}

    public function index(Request $request)
    {
        $offerType = $request->enum('offerType', OfferType::class) ?? OfferType::SELL;
        $product = Product::find($request->product);
        $products = Product::all();

        $offers = Offer::query()
            ->with(['user', 'currency', 'currency.paymentMethods', 'product'])
            ->where('offer_type', $offerType)
            ->when($product, fn($query) => $query->where('product_id', $product->id))
            ->paginate(10)
            ->through(fn($offer) => $offer->append([
                'display_amount',
                'display_rate'
            ]));

        return inertia('Offer/Index', compact(
            'offers',
            'offerType',
            'products',
            'product',
        ));
    }

    public function create(Request $request)
    {
        $products = Product::with(['currency'])->get();
        $currencies = Currency::get();
        $offerType = $request->enum('offerType', OfferType::class) ?? OfferType::SELL;
        $userId = $request->user()->id;
        $userProductAccount = UserProductAccount::query()->with(['userProductAccountItems' => [
            'productAccountItem',
            'productAccountItemOptions'
        ]])->where('user_id', $userId)->get();

        $userPaymentMethods = $this->offerService->getUserPaymentMethods($userId);

        return inertia('Offer/Edit', compact(
            'products',
            'currencies',
            'offerType',
            'userProductAccount',
            'userPaymentMethods'
        ));
    }

    public function store(StoreOfferRequest $request)
    {
        // 一時預かり用アカウント
        $adminProductAccount = Product::find($request->product_id)
            ->adminProductAccounts()
            ->where('is_temporary', true)
            ->first();

        $offer = DB::transaction(function () use ($request, $adminProductAccount) {
            $offerNo = $this->numberingService->generateOfferNumber();
            Offer::lockForUpdate()->where('offer_no', $offerNo)->get();

            $creatingOffer = Offer::create([
                ...$request->validated(),
                'offer_no' => $offerNo,
                'request_amount' => $request->amount,
                'amount' => $request->amount,
                'offer_status' => match ($request->enum('offer_type', OfferType::class)) {
                    OfferType::BUY => OfferStatus::PUBLISHING__ACTIVE,
                    OfferType::SELL => OfferStatus::WAIT_PUBLISH__WAIT_PRODUCT,
                },
                'problem_report_status' => ProblemReportStatus::NONE,
                'requested_at' => now(),
                'admin_product_account_id' => $adminProductAccount->id,
            ]);

            //TODO::Update user paymentmenthod will be from user profile payment method
            // $this->offerService->updateUserPaymentMethod($creatingOffer, $request->payment_methods);

            return $creatingOffer;
        });

        if($offer->offer_type === OfferType::SELL){
            JobsOfferSendingProductTimeout::dispatch($offer)->delay($offer->requested_at->addMinutes(15));
        }

        return to_route('offers.detail', ['offer' => $offer->offer_no]);
    }

    public function edit(Request $request, Offer $offer)
    {
        $offer = $offer->load('product');
        $products = Product::with(['currency'])->get();
        $currencies = Currency::where('id', $offer->currency_id)->get();
        $offerType = $offer->offer_type;
        $userId = $request->user()->id;

        $userProductAccount = UserProductAccount::query()->with(['userProductAccountItems' => [
            'productAccountItem',
            'productAccountItemOptions'
        ]])->where('user_id', $userId)->get();

        $userPaymentMethods = $this->offerService->getUserPaymentMethodByOfferType($offerType, $userId);

        return inertia('Offer/Edit', compact(
            'offer',
            'products',
            'currencies',
            'offerType',
            'userProductAccount',
            'userPaymentMethods'
        ));
    }

    public function update(UpdateOfferRequest $request, Offer $offer)
    {
        $offer->update($request->validated());
        //TODO::Update user paymentmenthod will be from user profile payment method
        // $this->offerService->updateUserPaymentMethod($offer,$request->payment_methods);

        return to_route('offers.detail', ['offer' => $offer->offer_no]);
    }

    public function detail(Request $request, Offer $offer)
    {
        $offer = $offer->load('product','currency','adminProductAccount.adminProductAccountItems');
        $offer = $offer->append('display_rate');
        $userId = $request->user()->id;
        
        $userProductAccount = UserProductAccount::query()->with(['userProductAccountItems' => [
            'productAccountItem',
            'productAccountItemOptions'
        ]])->where(['user_id'=>$userId, 'product_id'=>$offer->product_id])->first();//TODO::Get from offerModel
        $userPaymentMethods = $this->offerService->getUserPaymentMethodByOfferType($offer->offer_type, $userId);
       
        return inertia('Offer/Detail', compact(
            'offer',
            'userProductAccount',
            'userPaymentMethods'
        ));
    }

    public function cancel(Offer $offer, Request $request)
    {
        $nextStatus = $this->offerService->determineNextStatusForCancel($offer);
        if($nextStatus){
            $canUpdate = $this->offerService->canUpdateOfferStatus($offer, $nextStatus);
            if($canUpdate){
                $this->offerService->updateStatus($offer, $nextStatus);
            }
        }
        
        return to_route('offers.detail', ['offer' => $offer->offer_no]);
    }

    public function sendProduct(Offer $offer, Request $request)
    {
        $canUpdate = $this->offerService->canUpdateOfferStatus($offer, OfferStatus::WAIT_PUBLISH__CHECK_PRODUCT);
        if($canUpdate){
            $this->offerService->updateStatus($offer, OfferStatus::WAIT_PUBLISH__CHECK_PRODUCT);
        }

        return to_route('offers.detail', ['offer' => $offer->offer_no]);
    }

    public function pause(Offer $offer, Request $request)
    {
        $canUpdate = $this->offerService->canUpdateOfferStatus($offer, OfferStatus::PUBLISHING__PAUSE);
        if($canUpdate){
            $this->offerService->updateStatus($offer, OfferStatus::PUBLISHING__PAUSE);
        }

        return to_route('offers.detail', ['offer' => $offer->offer_no]);
    }

    public function active(Offer $offer, Request $request)
    {
        $canUpdate = $this->offerService->canUpdateOfferStatus($offer, OfferStatus::PUBLISHING__ACTIVE);
        if($canUpdate){
            $this->offerService->updateStatus($offer, OfferStatus::PUBLISHING__ACTIVE);
        }

        return to_route('offers.detail', ['offer' => $offer->offer_no]);
    }
}
