<?php

use App\Models\Currency;
use App\Models\Product;
use App\Models\AdminProductAccount;
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
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('offer_no')->unique()->comment('オファー番号');
            $table->unsignedTinyInteger('offer_type')->comment('オファー種類');
            $table->foreignIdFor(User::class)->constrained()->comment('ユーザーID');
            $table->foreignIdFor(Product::class)->constrained()->comment('商品ID');
            $table->double('request_amount')->comment('申請時数量');
            $table->double('amount')->comment('総数量');
            $table->foreignIdFor(Currency::class)->constrained()->comment('通貨ID');
            $table->double('min_amount')->comment('最小トレード可能額');
            $table->double('max_amount')->nullable()->comment('最大トレード可能額');
            $table->unsignedTinyInteger('rate_type')->comment('レート種類');
            $table->double('rate')->comment('レート');
            $table->unsignedInteger('time_limit')->comment('支払いまでの時間');
            $table->unsignedTinyInteger('offer_status')->comment('オファーステータス');
            $table->unsignedTinyInteger('problem_report_status')->comment('問題報告ステータス');
            $table->text('note')->comment('備考');
            $table->dateTime('requested_at')->comment('申請日時');
            $table->dateTime('published_at')->nullable()->comment('掲載開始日時');
            $table->foreignIdFor(AdminProductAccount::class)->nullable()->constrained()->comment('一時預かり用運営アカウントID');
            $table->timestamps();
            $table->comment('オファー');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};
