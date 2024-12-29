<?php

namespace App\Http\Controllers;

use App\Enums\KycStatus;
use App\Enums\PaymentItemType;
use App\Enums\ProductAccountItemType;
use App\Http\Requests\User\StoreUserKycApplicationRequest;
use App\Http\Requests\User\StoreUserProductAccountRequest;
use App\Http\Requests\User\StoreUserSellerPaymentRequest;
use App\Http\Requests\User\UpdateProfileRequest;
use App\Http\Requests\User\UpdateUserEmailRequest;
use App\Http\Requests\User\UpdateUserPasswordRequest;
use App\Http\Requests\User\UpdateUserProductAccountRequest;
use App\Http\Requests\User\UpdateUserSellerPaymentRequest;
use App\Models\BuyerPayment;
use App\Models\KycApplication;
use App\Models\KycImage;
use App\Models\Offer;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\SellerPayment;
use App\Models\User;
use App\Models\UserProductAccount;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function showMyProfile(Request $request)
    {
        $user = User::with(['recentAchievement'])
            ->withCount(['buyerPayments','sellerPayments','userProductAccounts'])
            ->find($request->user()->id);
        return inertia('User/MyProfile', compact(
            'user',
        ));
    }

    public function showProfile(Request $request, User $user)
    {
        if ($request->user()->id === $user->id) {
            return to_route('profile.show_my_profile');
        }

        $user->load(['recentAchievement']);
        $offers = Offer::query()
            ->with(['user', 'currency'])
            ->where('user_id', $user->id)
            ->get();
        $offers->append([
            'display_amount',
        ])->toArray();

        return inertia('User/Show', compact(
            'user',
            'offers',
        ));
    }

    public function editPassword(Request $request)
    {
        return inertia('User/Password');
    }

    public function updatePassword(UpdateUserPasswordRequest $request)
    {
        $user = $request->user();
        if(!(Hash::check($request->nowPassword, $user->password))){
            return to_route('password.edit')->withErrors(['nowPassword' => '現在のパスワードと一致しません。']);
        }

        $status = Password::reset(
            [
                'email' => $user->email,
                'password' => $request->newPassword,
                'password_confirmation' => $request->newPassword_confirmation,
                'token' => Password::createToken($user)
            ],
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ]);

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? to_route('profile.show_my_profile')->with('status', __($status))
            : back()->withErrors(['password' => [__($status)]]);
    }

    public function editEmail(Request $request)
    {
        return inertia('User/Email');
    }

    public function updateEmail(UpdateUserEmailRequest $request)
    {
        $newEmail = $request->email;
        $request->user()->update([
            'email' => $newEmail
        ]);

        return to_route('profile.show_my_profile');
    }

    public function createKycApplication(Request $request)
    {
        return inertia('User/KycApplication');
    }

    public function storeKycApplication(StoreUserKycApplicationRequest $request)
    {
        if($request->user()->kyc_status != KycStatus::UNCONFIRMED){
            return to_route('kyc_application.create')->withErrors('審査中または確認済のです');
        }

        DB::transaction(function () use ($request) {
            $kycApplication = KycApplication::create(
                    [
                        'user_id' => $request->user()->id,
                        'is_approved' => '0',
                    ]
                );

            $request->user()->update([
                'kyc_status' => KycStatus::REVIEWING
            ]);

            KycImage::create(
                [
                    'kyc_application_id' => $kycApplication->id,
                    'image_path' => $request->file('kycImage')->store('kyc_image', 's3_secure'),
                ]
            );

            KycImage::create(
                [
                    'kyc_application_id' => $kycApplication->id,
                    'image_path' => $request->file('kycPhoto')->store('kyc_image', 's3_secure'),
                ]
            );
         });
        return to_route('profile.show_my_profile');
    }

    public function update(UpdateProfileRequest $request)
    {
        $request->user()->update($request->validated());

        return to_route('profile.show_my_profile');
    }

    public function showBuyerPayment(Request $request)
    {
        $user = User::with(['recentAchievement'])
            ->find($request->user()->id);
        $registeredPaymentMethods = BuyerPayment::query()
            ->with(['paymentMethod'])
            ->where('user_id', $request->user()->id)
            ->orderBy('payment_method_id', 'asc')
            ->get();
        $unregisteredPaymentMethods = PaymentMethod::query()
            ->whereNotIn('id',$registeredPaymentMethods->select('payment_method_id'))
            ->get();
        return inertia('User/BuyerPayment/Show', compact(
            'user',
            'registeredPaymentMethods',
            'unregisteredPaymentMethods',
        ));
    }

    public function storeBuyerPayment(Request $request)
    {
        DB::transaction(function () use ($request) {
            $buyerPaymentExist = BuyerPayment::query()
                ->where([
                    ['user_id', '=', $request->user()->id],
                    ['payment_method_id', '=', $request->payment_method_id],
                ])
                ->exists();
            if($buyerPaymentExist){
                return to_route('buyer_payment.show')->withErrors(['msg' => '登録済みの支払方法です']);
            }

            BuyerPayment::create(
                [
                    'user_id' => $request->user()->id,
                    'payment_method_id' => $request->payment_method_id,
                    'is_active' => true
                ]
            );
        });

        return to_route('buyer_payment.show');
    }

    public function deleteBuyerPayment(Request $request, BuyerPayment $buyerPayment)
    {
        if ($request->user()->cannot('edit', $buyerPayment)) {
            return to_route('profile.show_my_profile');
        }
        $buyerPayment->delete();

        return to_route('buyer_payment.show');
    }

    public function activeBuyerPaymentToggle(Request $request, BuyerPayment $buyerPayment)
    {
        if ($request->user()->cannot('edit', $buyerPayment)) {
            return to_route('profile.show_my_profile');
        }
        $buyerPayment->update([
            'is_active' => $buyerPayment->is_active == true ? false : true
        ]);

        return to_route('buyer_payment.show');
    }

    public function showSellerPayment(Request $request)
    {
        $user = User::with(['recentAchievement'])
            ->find($request->user()->id);
        $userSellerPayments = SellerPayment::query()
            ->with(['paymentMethod','sellerPaymentItems' =>[
                'paymentItem',
                'paymentOptions'
            ]])
            ->where('user_id', $user->id)
            ->orderBy('payment_method_id', 'asc')
            ->get();

        return inertia('User/SellerPayment/Show', compact(
            'user',
            'userSellerPayments',
        ));
    }

    public function createSellerPayment(Request $request)
    {
        $userSellerPayments = SellerPayment::query()
            ->where('user_id', $request->user()->id)
            ->get();
        $paymentMethods = PaymentMethod::with(['paymentItems.paymentOptions'])
            ->whereNotIn('id',$userSellerPayments->select('payment_method_id'))
            ->get();

        return inertia('User/SellerPayment/Edit', compact(
            'paymentMethods'
        ));
    }

    public function storeSellerPayment(StoreUserSellerPaymentRequest $request)
    {
        DB::transaction(function () use ($request) {
            $sellerPaymentExist = SellerPayment::query()
            ->where([
                ['user_id', '=', $request->user()->id],
                ['payment_method_id', '=', $request->validated('paymentMethod_id')],
              ])
              ->exists();

            if($sellerPaymentExist){
                return to_route('seller_payment.show')->withErrors(['msg' => '登録済みの支払先情報です']);
            }

            $userSellerPayment = SellerPayment::create(
                [
                    'user_id' => $request->user()->id,
                    'payment_method_id' => $request->validated('paymentMethod_id'),
                    'is_active' => true,
                ]
            );
            foreach ($request->validated('seller_payment_items') as $item) {
                switch (PaymentItemType::from($item['type'])) {
                    case PaymentItemType::TEXT:
                        $userSellerPayment->sellerPaymentItems()->create($item);
                        break;
                    case PaymentItemType::SELECT:
                    case PaymentItemType::CHECKBOX:
                    case PaymentItemType::RADIO:
                        $sellerPaymenItem = $userSellerPayment->sellerPaymentItems()->create([
                            ...$item,
                            'value' => null,
                        ]);
                        $sellerPaymenItem->paymentOptions()->attach($item['value']);
                        break;
                    case PaymentItemType::IMAGE:
                        $userSellerPayment->sellerPaymentItems()->create([
                            ...$item,
                            'value' => $item['value']->store('seller_payment_item_images'),
                        ]);
                }
            }
        });
        return to_route('seller_payment.show');
    }

    public function editSellerPayment(Request $request, SellerPayment $sellerPayment)
    {
        if ($request->user()->cannot('edit', $sellerPayment)) {
            return to_route('profile.show_my_profile');
        }

        $paymentMethods = PaymentMethod::all();

        $sellerPayment->load([
            'paymentMethod',
            'sellerPaymentItems.paymentItem.paymentOptions',
            'sellerPaymentItems.paymentOptions',
        ]);

        return inertia('User/SellerPayment/Edit', compact(
            'paymentMethods',
            'sellerPayment',
        ));
    }

    public function updateSellerPayment(UpdateUserSellerPaymentRequest $request)
    {
        if ($request->user()->cannot('edit', SellerPayment::find($request->id))) {
            return to_route('profile.show_my_profile');
        }
        DB::transaction(function () use ($request) {
            $userSellerPayment = SellerPayment::create(
                [
                    'user_id' => $request->user()->id,
                    'payment_method_id' => $request->validated('paymentMethod_id'),
                    'is_active' => true,
                ]
            );
            foreach ($request->validated('seller_payment_items') as $item) {
                switch (PaymentItemType::from($item['type'])) {
                    case PaymentItemType::TEXT:
                        $userSellerPayment->sellerPaymentItems()->create($item);
                        break;
                    case PaymentItemType::SELECT:
                    case PaymentItemType::CHECKBOX:
                    case PaymentItemType::RADIO:
                        $sellerPaymenItem = $userSellerPayment->sellerPaymentItems()->create([
                            ...$item,
                            'value' => null,
                        ]);
                        $sellerPaymenItem->paymentOptions()->attach($item['value']);
                        break;
                    case PaymentItemType::IMAGE:
                        $userSellerPayment->sellerPaymentItems()->create([
                            ...$item,
                            'value' => $item['value']->store('seller_payment_item_images'),
                        ]);
                }
            }
        });

        SellerPayment::find($request->id)->delete();
        return to_route('seller_payment.show');
    }

    public function deleteSellerPayment(Request $request, SellerPayment $sellerPayment)
    {
        if ($request->user()->cannot('edit', $sellerPayment)) {
            return to_route('profile.show_my_profile');
        }
        $sellerPayment->delete();

        return to_route('seller_payment.show');
    }

    public function activeSellerPaymentToggle(Request $request, SellerPayment $sellerPayment)
    {
        if ($request->user()->cannot('edit', $sellerPayment)) {
            return to_route('profile.show_my_profile');
        }
        $sellerPayment->update([
            'is_active' => $sellerPayment->is_active == true ? false : true
        ]);

        return to_route('seller_payment.show');
    }

    public function showProductAccount(Request $request)
    {
        $user = User::with(['recentAchievement'])
            ->find($request->user()->id);
        $userProductAccounts = UserProductAccount::query()
            ->with(['product','userProductAccountItems' =>[
                'productAccountItem',
                'productAccountItemOptions'
            ]])
            ->where('user_id', $user->id)
            ->orderBy('product_id', 'asc')
            ->get();

        return inertia('User/ProductAccount/Show', compact(
            'user',
            'userProductAccounts',
        ));
    }

    public function createProductAccount(Request $request)
    {
        $userProductAccounts = UserProductAccount::query()
            ->where('user_id', $request->user()->id)
            ->get();

        $products = Product::with(['productAccountItems.productAccountItemOptions'])
            ->whereNotIn('id', $userProductAccounts->select('product_id'))
            ->get();

        return inertia('User/ProductAccount/Edit', compact(
            'products'
        ));
    }

    public function storeProductAccount(StoreUserProductAccountRequest $request)
    {
        DB::transaction(function () use ($request) {
            $userProductsExist = UserProductAccount::query()
            ->where([
                ['user_id', '=', $request->user()->id],
                ['product_id', '=', $request->validated('product_id')],
              ])
              ->exists();

            if($userProductsExist){
                return to_route('user_product_account.show')->withErrors(['msg' => '登録済みの商品送付先情報です']);
            }

            $userProductAccount = UserProductAccount::create(
                [
                    'user_id' => $request->user()->id,
                    'product_id' => $request->validated('product_id'),
                ]
            );
            foreach ($request->validated('user_product_account_items') as $item) {
                switch (ProductAccountItemType::from($item['type'])) {
                    case ProductAccountItemType::TEXT:
                        $userProductAccount->userProductAccountItems()->create($item);
                        break;
                    case ProductAccountItemType::SELECT:
                    case ProductAccountItemType::CHECKBOX:
                    case ProductAccountItemType::RADIO:
                        $userProductAccountItem = $userProductAccount->userProductAccountItems()->create([
                            ...$item,
                            'value' => null,
                        ]);
                        $userProductAccountItem->productAccountItemOptions()->attach($item['value']);
                        break;
                    case ProductAccountItemType::IMAGE:
                        $userProductAccount->userProductAccountItems()->create([
                            ...$item,
                            'value' => $item['value']->store('product_account_item_images'),
                        ]);
                }
            }
        });

        return to_route('user_product_account.show');
    }

    public function editProductAccount(Request $request, UserProductAccount $userProductAccount)
    {
        if ($request->user()->cannot('edit', $userProductAccount)) {
            return to_route('profile.show_my_profile');
        }

        $products = Product::all();

        $userProductAccount->load([
            'product',
            'userProductAccountItems.productAccountItem.productAccountItemOptions',
            'userProductAccountItems.productAccountItemOptions'
        ]);

        return inertia('User/ProductAccount/Edit', compact(
            'userProductAccount',
            'products',
        ));
    }

    public function updateProductAccount(UpdateUserProductAccountRequest $request)
    {
        if ($request->user()->cannot('edit', UserProductAccount::find($request->id))) {
            return to_route('profile.show_my_profile');
        }
        DB::transaction(function () use ($request) {

            $userProductAccount = UserProductAccount::create(
                [
                    'user_id' => $request->user()->id,
                    'product_id' => $request->validated('product_id'),
                ]
            );
            foreach ($request->validated('user_product_account_items') as $item) {
                switch (ProductAccountItemType::from($item['type'])) {
                    case ProductAccountItemType::TEXT:
                        $userProductAccount->userProductAccountItems()->create($item);
                        break;
                    case ProductAccountItemType::SELECT:
                    case ProductAccountItemType::CHECKBOX:
                    case ProductAccountItemType::RADIO:
                        $userProductAccountItem = $userProductAccount->userProductAccountItems()->create([
                            ...$item,
                            'value' => null,
                        ]);
                        $userProductAccountItem->productAccountItemOptions()->attach($item['value']);
                        break;
                    case ProductAccountItemType::IMAGE:
                        $userProductAccount->userProductAccountItems()->create([
                            ...$item,
                            'value' => is_string($item['value'])? $item['value']:$item['value']->store('product_account_item_images'),
                        ]);
                }
            }
        });

        UserProductAccount::find($request->id)->delete();
        return to_route('user_product_account.show');
    }

    public function deleteProductAccount(Request $request, UserProductAccount $userProductAccount)
    {
        if ($request->user()->cannot('edit', $userProductAccount)) {
            return to_route('profile.show_my_profile');
        }

        $userProductAccount->delete();

        return to_route('user_product_account.show');
    }
}
