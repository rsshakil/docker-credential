<?php

namespace Database\Seeders;

use App\Enums\KycStatus;
use App\Enums\Language;
use App\Enums\OfferStatus;
use App\Enums\OfferType;
use App\Enums\PaymentItemType;
use App\Enums\ProblemReportStatus;
use App\Enums\ProblemUser;
use App\Enums\ProductAccountItemType;
use App\Enums\RateType;
use App\Enums\Role;
use App\Enums\TradeStatus;
use App\Enums\UserStatus;
use App\Models\Administrator;
use App\Models\BuyerPayment;
use App\Models\Currency;
use App\Models\Offer;
use App\Models\PaymentItem;
use App\Models\PaymentMethod;
use App\Models\PaymentOption;
use App\Models\Product;
use App\Models\ProductAccountItem;
use App\Models\ProductAccountItemOption;
use App\Models\AdminProductAccount;
use App\Models\AdminProductAccountItem;
use App\Models\KycApplication;
use App\Models\RecentAchievement;
use App\Models\SellerPayment;
use App\Models\SellerPaymentItem;
use App\Models\Trade;
use App\Models\User;
use App\Models\UserProductAccount;
use App\Models\UserProductAccountItem;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Administrator::factory()
            ->sequence(
                [
                    'name' => 'SV権限ユーザー',
                    'email' => 'admin@example.com',
                    'password' => 'password',
                    'role' => Role::SV,
                ],
                [
                    'name' => 'OP権限ユーザー',
                    'email' => 'op@example.com',
                    'password' => 'password',
                    'role' => Role::OP,
                ],
            )
            ->count(2)
            ->create();

        $users = User::factory()
            ->sequence(
                [
                    'user_no' => 1234567890,
                    'name' => 'Austen',
                    'email' => 'user@example.com',
                    'password' => 'password',
                    'language' => Language::JP,
                    'user_status' => UserStatus::SAMPLE,
                    'kyc_status' => KycStatus::UNCONFIRMED,
                    'full_name' => 'Austen Olson',
                ],
                [
                    'user_no' => 987654321,
                    'name' => 'Osborne',
                    'email' => 'user2@example.com',
                    'password' => 'password',
                    'language' => Language::JP,
                    'user_status' => UserStatus::SAMPLE,
                    'kyc_status' => KycStatus::UNCONFIRMED,
                    'full_name' => 'Osborne Abernathy',
                ],
            )
            ->count(2)
            ->create();

        $users->each(fn ($user) => RecentAchievement::factory()
            ->sequence(
                [
                    'user_id' => $users[0]->id,
                ],
                [
                    'user_id' => $users[1]->id,
                ],
            )
            ->count(2)
            ->create()
        );

        $users->each(fn ($user) => KycApplication::factory()
            ->sequence(
                [
                    'user_id' => $users[0]->id,
                ],
                [
                    'user_id' => $users[1]->id,
                ],
            )
            ->count(2)
            ->create()
        );

        $currencies = Currency::factory()
            ->sequence(
                [
                    'name' => '日本円',
                    'code' => 'JPY',
                    'scale' => 0,
                    'min_amount' => 100,
                ],
                [
                    'name' => '米ドル',
                    'code' => 'USD',
                    'scale' => 2,
                    'min_amount' => 1,
                ],
            )
            ->count(2)
            ->create();
        $currencies[0]->currencyRates()->attach([1], ['rate' => 1]);
        $currencies[0]->currencyRates()->attach([2], ['rate' => 0.0066523]);
        $currencies[1]->currencyRates()->attach([1], ['rate' => 150.324]);
        $currencies[1]->currencyRates()->attach([2], ['rate' => 1]);

        $paymentMethods = PaymentMethod::factory()
            ->sequence(
                [
                    'currency_id' => $currencies[0]->id,
                    'name' => '銀行振込',
                    'logo_path' => 'dummy',
                ],
                [
                    'currency_id' => $currencies[0]->id,
                    'name' => 'PayPay',
                    'logo_path' => 'dummy',
                ],
                [
                    'currency_id' => $currencies[1]->id,
                    'name' => 'Paypal',
                    'logo_path' => 'dummy',
                ]
            )
            ->count(3)
            ->create();
        $paymentItems = PaymentItem::factory()
            ->sequence(
                [
                    'payment_method_id' => $paymentMethods[0]->id,
                    'type' => PaymentItemType::TEXT,
                    'name' => '金融機関名',
                ],
                [
                    'payment_method_id' => $paymentMethods[0]->id,
                    'type' => PaymentItemType::TEXT,
                    'name' => '支店',
                ],
                [
                    'payment_method_id' => $paymentMethods[0]->id,
                    'type' => PaymentItemType::TEXT,
                    'name' => '口座名義',
                ],
                [
                    'payment_method_id' => $paymentMethods[0]->id,
                    'type' => PaymentItemType::TEXT,
                    'name' => '口座番号',
                ],
                [
                    'payment_method_id' => $paymentMethods[0]->id,
                    'type' => PaymentItemType::RADIO,
                    'name' => '預金種目',
                ],
                [
                    'payment_method_id' => $paymentMethods[1]->id,
                    'type' => PaymentItemType::TEXT,
                    'name' => 'PayPayID',
                ],
                [
                    'payment_method_id' => $paymentMethods[1]->id,
                    'type' => PaymentItemType::IMAGE,
                    'name' => 'QRコード',
                ],
                [
                    'payment_method_id' => $paymentMethods[1]->id,
                    'type' => PaymentItemType::IMAGE,
                    'name' => 'QRコード2',
                ],
                [
                    'payment_method_id' => $paymentMethods[2]->id,
                    'type' => PaymentItemType::TEXT,
                    'name' => '名前',
                ],
                [
                    'payment_method_id' => $paymentMethods[2]->id,
                    'type' => PaymentItemType::TEXT,
                    'name' => 'Email',
                ],
            )
            ->count(9)
            ->create();
        $paymentOptions = PaymentOption::factory()
            ->sequence(
                [
                    'payment_item_id' => $paymentItems[4]->id,
                    'name' => '普通預金',
                ],
                [
                    'payment_item_id' => $paymentItems[4]->id,
                    'name' => '当座預金',
                ]
            )
            ->count(2)
            ->create();

        BuyerPayment::factory()
            ->sequence(
                [
                    'user_id' => $users[0]->id,
                    'payment_method_id' => $paymentMethods[0]->id,
                    'is_active' => true,
                ],
                [
                    'user_id' => $users[1]->id,
                    'payment_method_id' => $paymentMethods[0]->id,
                    'is_active' => true,
                ],
            )
            ->count(2)
            ->create();

        $sellerPayments = SellerPayment::factory()
            ->sequence(
                [
                    'user_id' => $users[0]->id,
                    'payment_method_id' => $paymentMethods[0]->id,
                    'is_active' => true,
                ],
                [
                    'user_id' => $users[1]->id,
                    'payment_method_id' => $paymentMethods[0]->id,
                    'is_active' => true,
                ],
            )
            ->count(2)
            ->create();
        $sellerPaymentItems = SellerPaymentItem::factory()
            ->sequence(
                [
                    'seller_payment_id' => $sellerPayments[0]->id,
                    'payment_item_id' => $paymentItems[0]->id,
                    'value' => '三井住友銀行',
                ],
                [
                    'seller_payment_id' => $sellerPayments[0]->id,
                    'payment_item_id' => $paymentItems[1]->id,
                    'value' => '本店',
                ],
                [
                    'seller_payment_id' => $sellerPayments[0]->id,
                    'payment_item_id' => $paymentItems[2]->id,
                    'value' => 'Austen Olson',
                ],
                [
                    'seller_payment_id' => $sellerPayments[0]->id,
                    'payment_item_id' => $paymentItems[3]->id,
                    'value' => '1234567',
                ],
                [
                    'seller_payment_id' => $sellerPayments[0]->id,
                    'payment_item_id' => $paymentItems[4]->id,
                    'value' => null,
                ],
                [
                    'seller_payment_id' => $sellerPayments[1]->id,
                    'payment_item_id' => $paymentItems[0]->id,
                    'value' => '三菱UFJ銀行',
                ],
                [
                    'seller_payment_id' => $sellerPayments[1]->id,
                    'payment_item_id' => $paymentItems[1]->id,
                    'value' => '本店',
                ],
                [
                    'seller_payment_id' => $sellerPayments[1]->id,
                    'payment_item_id' => $paymentItems[2]->id,
                    'value' => 'Osborne Abernathy',
                ],
                [
                    'seller_payment_id' => $sellerPayments[1]->id,
                    'payment_item_id' => $paymentItems[3]->id,
                    'value' => '2634392',
                ],
                [
                    'seller_payment_id' => $sellerPayments[1]->id,
                    'payment_item_id' => $paymentItems[4]->id,
                    'value' => null,
                ],
            )
            ->count(10)
            ->create();
        $sellerPaymentItems[4]->paymentOptions()->attach([$paymentOptions[0]->id]);
        $sellerPaymentItems[9]->paymentOptions()->attach([$paymentOptions[0]->id]);

        $products = Product::factory()
            ->sequence(
                [
                    'name' => 'ゲームドル',
                    'logo_path' => 'dummy',
                    'unit' => 'Game$',
                    'scale' => 2,
                    'currency_id' => $currencies[1]->id,
                    'base_currency_rate' => 1,
                    'trade_fee' => 0.025,
                    'refund_fee' => 3,
                ],
                [
                    'name' => 'xコイン',
                    'logo_path' => 'dummy',
                    'unit' => 'COIN',
                    'scale' => 0,
                    'currency_id' => $currencies[1]->id,
                    'base_currency_rate' => 1.2,
                    'trade_fee' => 0.023,
                    'refund_fee' => 3,
                ],
            )
            ->count(2)
            ->create();

        foreach ($products as $product) {

            $productAccountItems = ProductAccountItem::factory()
                ->for($product)
                ->sequence(
                    [
                        'name' => 'テキスト項目',
                        'type' => ProductAccountItemType::TEXT,
                    ],
                    [
                        'name' => 'セレクトボックス項目',
                        'type' => ProductAccountItemType::SELECT,
                    ],
                    [
                        'name' => 'チェックボックス項目',
                        'type' => ProductAccountItemType::CHECKBOX,
                    ],
                    [
                        'name' => 'ラジオボタン項目',
                        'type' => ProductAccountItemType::RADIO,
                    ],
                    [
                        'name' => '画像項目',
                        'type' => ProductAccountItemType::IMAGE,
                    ],
                    [
                        'name' => '画像項目2',
                        'type' => ProductAccountItemType::IMAGE,
                    ],
                )
                ->count(6)
                ->create();

            $productAccountItemOptionsA = ProductAccountItemOption::factory()
                ->for($productAccountItems[1])
                ->sequence(
                    ['name' => 'select type 1'],
                    ['name' => 'select type 2'],
                )
                ->count(2)
                ->create();

            $productAccountItemOptionsB = ProductAccountItemOption::factory()
                ->for($productAccountItems[2])
                ->sequence(
                    ['name' => 'check type 1'],
                    ['name' => 'check type 2'],
                )
                ->count(2)
                ->create();

            $productAccountItemOptionsC = ProductAccountItemOption::factory()
                ->for($productAccountItems[3])
                ->sequence(
                    ['name' => 'radio type 1'],
                    ['name' => 'radio type 2'],
                )
                ->count(2)
                ->create();

            $adminProductAccount = AdminProductAccount::factory()
                ->for($product)
                ->state([
                    'name' => 'デフォルトアカウント',
                    'is_temporary' => true,
                    'is_send' => true,
                ])
                ->create();

            AdminProductAccountItem::factory()
                ->for($adminProductAccount)
                ->for($productAccountItems[0])
                ->state(['value' => fake()->bothify('admin *****')])
                ->create();
            AdminProductAccountItem::factory()
                ->for($adminProductAccount)
                ->for($productAccountItems[1])
                ->hasAttached($productAccountItemOptionsA[0])
                ->create();
            AdminProductAccountItem::factory()
                ->for($adminProductAccount)
                ->for($productAccountItems[2])
                ->hasAttached([$productAccountItemOptionsB[0], $productAccountItemOptionsB[1]])
                ->create();
            AdminProductAccountItem::factory()
                ->for($adminProductAccount)
                ->for($productAccountItems[3])
                ->hasAttached($productAccountItemOptionsC[0])
                ->create();
            AdminProductAccountItem::factory()
                ->for($adminProductAccount)
                ->for($productAccountItems[4])
                ->state(['value' => 'admin QRPath'])
                ->create();

            foreach ($users as $user) {
                $userProductAccount = UserProductAccount::factory()
                    ->for($user)
                    ->for($product)
                    ->create();

                UserProductAccountItem::factory()
                    ->for($userProductAccount)
                    ->for($productAccountItems[0])
                    ->state(['value' => fake()->bothify($user->name . ' *****')])
                    ->create();
                UserProductAccountItem::factory()
                    ->for($userProductAccount)
                    ->for($productAccountItems[1])
                    ->hasAttached($productAccountItemOptionsA[1])
                    ->create();
                UserProductAccountItem::factory()
                    ->for($userProductAccount)
                    ->for($productAccountItems[2])
                    ->hasAttached([$productAccountItemOptionsB[0], $productAccountItemOptionsB[1]])
                    ->create();
                UserProductAccountItem::factory()
                    ->for($userProductAccount)
                    ->for($productAccountItems[3])
                    ->hasAttached($productAccountItemOptionsC[1])
                    ->create();
                UserProductAccountItem::factory()
                    ->for($userProductAccount)
                    ->for($productAccountItems[4])
                    ->state(['value' => $user->name . ' QRPath'])
                    ->create();
            }
        }

        Offer::factory()
            ->state([
                'offer_no' => 10000001,
                'offer_type' => OfferType::SELL,
                'user_id' => $users[1]->id,
                'product_id' => $products[0]->id,
                'request_amount' => 100_000,
                'amount' => 100_000,
                'currency_id' => $currencies[0]->id,
                'min_amount' => 250,
                'max_amount' => 10000,
                'rate_type' => RateType::VARIABLE,
                'rate' => 105,
                'time_limit' => 30,
                'offer_status' => OfferStatus::WAIT_PUBLISH__WAIT_PRODUCT,
                'problem_report_status' => ProblemReportStatus::NONE,
                'note' => 'ここに備考を入力',
                'requested_at' => now(),
                'admin_product_account_id' => AdminProductAccount::first(),
            ])
            ->afterCreating(function (Offer $offer) use ($users) {
                $history = $offer->offerHistories()->make([
                    'result_status' => OfferStatus::WAIT_PUBLISH__WAIT_PRODUCT,
                ]);
                $history->operator()->associate($users[1]);
                $history->save();
            })
            ->create();

        Offer::factory()
            ->state([
                'offer_no' => 10000002,
                'offer_type' => OfferType::SELL,
                'user_id' => $users[1]->id,
                'product_id' => $products[1]->id,
                'request_amount' => 100_000,
                'amount' => 100_000,
                'currency_id' => $currencies[0]->id,
                'min_amount' => 250,
                'max_amount' => null,
                'rate_type' => RateType::FIXED,
                'rate' => 150.50,
                'time_limit' => 15,
                'offer_status' => OfferStatus::WAIT_PUBLISH__WAIT_PRODUCT,
                'problem_report_status' => ProblemReportStatus::NONE,
                'note' => 'ここに備考を入力',
                'requested_at' => now(),
                'admin_product_account_id' => AdminProductAccount::first(),
            ])
            ->afterCreating(function (Offer $offer) use ($users) {
                $history = $offer->offerHistories()->make([
                    'result_status' => OfferStatus::WAIT_PUBLISH__WAIT_PRODUCT,
                ]);
                $history->operator()->associate($users[1]);
                $history->save();
            })
            ->create();

        $offer = Offer::factory()
            ->state([
                'offer_no' => 10000003,
                'offer_type' => OfferType::SELL,
                'user_id' => $users[0]->id,
                'product_id' => $products[0]->id,
                'request_amount' => 100_000,
                'amount' => 100_000,
                'currency_id' => $currencies[0]->id,
                'min_amount' => 250,
                'max_amount' => null,
                'rate_type' => RateType::VARIABLE,
                'rate' => 105,
                'time_limit' => 30,
                'offer_status' => OfferStatus::PUBLISHING__ACTIVE,
                'problem_report_status' => ProblemReportStatus::NONE,
                'note' => 'ここに備考を入力',
                'requested_at' => now(),
                'published_at' => now(),
                'admin_product_account_id' => AdminProductAccount::first(),
            ])
            ->afterCreating(function (Offer $offer) use ($users) {
                $histories = $offer->offerHistories()->makeMany([
                    ['result_status' => OfferStatus::WAIT_PUBLISH__WAIT_PRODUCT],
                    ['result_status' => OfferStatus::WAIT_PUBLISH__CHECK_PRODUCT, 'created_at' => now()->addMinutes(1)],
                    ['result_status' => OfferStatus::PUBLISHING__ACTIVE, 'created_at' => now()->addMinutes(2)],
                ]);
                $histories[0]->operator()->associate($users[0]);
                $histories[1]->operator()->associate($users[0]);
                $histories[2]->operator()->associate(Administrator::first());
                $histories[2]->adminProductAccount()->associate(AdminProductAccount::first());
                $histories->each(fn($e) => $e->save());
            })
            ->create();

        Trade::factory()
            ->state([
                'trade_no' => 100000001,
                'offer_id' => $offer->id,
                'buyer_id' => $users[1]->id,
                'seller_id' => $users[0]->id,
                'product_id' => $products[0]->id,
                'trade_amount' => 100,
                'rate' => 150,
                'trade_status' => TradeStatus::UNPAID__WAIT_PAY,
                'buyer_problem_report_status' => ProblemReportStatus::NONE,
                'seller_problem_report_status' => ProblemReportStatus::NONE,
                'requested_at' => now(),
                'admin_product_account_id' => AdminProductAccount::first()->id,
                'seller_payment_id' => User::find(1)->sellerPayments[0]->id,
                'buyer_account_id' => User::find(2)->userProductAccounts()->where('product_id', $products[0]->id)->first()->id,
                'seller_account_id' => User::find(1)->userProductAccounts()->where('product_id', $products[0]->id)->first()->id,
                'problem_user' => ProblemUser::NONE,
            ])
            ->afterCreating(function (Trade $trade) use ($users) {
                $history = $trade->tradeHistories()->make([
                    'result_status' => TradeStatus::UNPAID__WAIT_PAY,
                ]);
                $history->operator()->associate($users[1]);
                $history->save();
            })
            ->create();

        $offer = Offer::factory()
            ->state([
                'offer_no' => 10000004,
                'offer_type' => OfferType::BUY,
                'user_id' => $users[0]->id,
                'product_id' => $products[0]->id,
                'request_amount' => 100_000,
                'amount' => 100_000,
                'currency_id' => $currencies[0]->id,
                'min_amount' => 250,
                'max_amount' => null,
                'rate_type' => RateType::VARIABLE,
                'rate' => 105,
                'time_limit' => 30,
                'offer_status' => OfferStatus::PUBLISHING__ACTIVE,
                'problem_report_status' => ProblemReportStatus::NONE,
                'note' => 'ここに備考を入力',
                'requested_at' => now(),
                'published_at' => now(),
                'admin_product_account_id' => AdminProductAccount::first(),
            ])
            ->afterCreating(function (Offer $offer) use ($users) {
                $history = $offer->offerHistories()->make([
                    'result_status' => OfferStatus::WAIT_PUBLISH__WAIT_PRODUCT,
                ]);
                $history->operator()->associate($users[0]);
                $history->save();
            })
            ->create();

        Trade::factory()
            ->state([
                'trade_no' => 100000002,
                'offer_id' => $offer->id,
                'buyer_id' => $users[0]->id,
                'seller_id' => $users[1]->id,
                'product_id' => $products[0]->id,
                'trade_amount' => 100,
                'rate' => 150,
                'trade_status' => TradeStatus::REQUEST__WAIT_PRODUCT,
                'buyer_problem_report_status' => ProblemReportStatus::NONE,
                'seller_problem_report_status' => ProblemReportStatus::NONE,
                'requested_at' => now(),
                'admin_product_account_id' => AdminProductAccount::first()->id,
                'seller_payment_id' => User::find(2)->sellerPayments[0]->id,
                'buyer_account_id' => User::find(1)->userProductAccounts()->where('product_id', $products[0]->id)->first()->id,
                'seller_account_id' => User::find(2)->userProductAccounts()->where('product_id', $products[0]->id)->first()->id,
                'problem_user' => ProblemUser::NONE,
            ])
            ->afterCreating(function (Trade $trade) use ($users) {
                $history = $trade->tradeHistories()->make([
                    'result_status' => TradeStatus::REQUEST__WAIT_PRODUCT,
                ]);
                $history->operator()->associate($users[1]);
                $history->save();
            })
            ->create();
    }
}
