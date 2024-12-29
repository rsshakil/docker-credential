<?php

use App\Models\AdminProductAccount;
use App\Models\Offer;
use App\Models\Product;
use App\Models\UserProductAccount;
use App\Models\SellerPayment;
use App\Models\User;
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
        Schema::create('trades', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('trade_no')->unique()->comment('トレード番号');
            $table->foreignIdFor(Offer::class)->constrained()->comment('オファーID');
            $table->foreignIdFor(User::class, 'buyer_id')->constrained('users')->comment('BuyerID');
            $table->foreignIdFor(User::class, 'seller_id')->constrained('users')->comment('SellerID');
            $table->foreignIdFor(Product::class)->constrained()->comment('商品ID');
            $table->double('trade_amount')->comment('商品数量');
            $table->double('rate')->comment('レート');
            $table->unsignedTinyInteger('trade_status')->comment('トレードステータス');
            $table->unsignedTinyInteger('buyer_problem_report_status')->comment('Buyer問題報告ステータス');
            $table->unsignedTinyInteger('seller_problem_report_status')->comment('Seller問題報告ステータス');
            $table->dateTime('requested_at')->comment('開始日時');
            $table->foreignIdFor(AdminProductAccount::class)->constrained()->comment('一時預かり用運営アカウントID');
            $table->foreignIdFor(SellerPayment::class)->constrained()->comment('支払先情報ID');
            $table->foreignIdFor(UserProductAccount::class, 'buyer_account_id')->constrained('user_product_accounts')->comment('Buyer商品送付先ID');
            $table->foreignIdFor(UserProductAccount::class, 'seller_account_id')->constrained('user_product_accounts')->comment('Seller商品送付先ID');
            $table->unsignedTinyInteger('problem_user')->comment('問題の責任ユーザー');
            $table->timestamps();
            $table->comment('取引');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trades');
    }
};