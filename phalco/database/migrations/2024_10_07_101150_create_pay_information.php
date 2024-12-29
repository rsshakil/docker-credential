<?php

use App\Models\PaymentMethod;
use App\Models\PaymentItem;
use App\Models\PaymentOption;
use App\Models\SellerPaymentItem;
use App\Models\User;
use App\Models\SellerPayment;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('buyer_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->comment('ユーザーID');
            $table->foreignIdFor(PaymentMethod::class)->constrained()->comment('支払方法ID');
            $table->boolean('is_active')->comment('アクティブ');
            $table->timestamps();
            $table->softDeletes()->comment('削除日時');
            $table->comment('支払情報');
        });
        Schema::create('seller_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->comment('ユーザーID');
            $table->foreignIdFor(PaymentMethod::class)->constrained()->comment('支払方法ID');
            $table->boolean('is_active')->comment('アクティブ');
            $table->timestamps();
            $table->softDeletes()->comment('削除日時');
            $table->comment('支払先情報');
        });
        Schema::create('seller_payment_items', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(SellerPayment::class)->constrained()->comment('支払先情報ID');
            $table->foreignIdFor(PaymentItem::class)->constrained()->comment('支払方法の項目定義ID');
            $table->string('value')->nullable()->comment('設定値');
            $table->timestamps();
            $table->comment('支払先情報の項目');
        });
        Schema::create('seller_payment_options', function (Blueprint $table) {
            $table->foreignIdFor(SellerPaymentItem::class)->constrained()->comment('支払先情報の項目ID');
            $table->foreignIdFor(PaymentOption::class)->constrained()->comment('支払方法の項目のオプション定義ID');
            $table->comment('支払先情報のオプション設定値');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buyer_payments');
        Schema::dropIfExists('seller_payments');
        Schema::dropIfExists('seller_payment_items');
        Schema::dropIfExists('seller_payment_options');
    }
};
