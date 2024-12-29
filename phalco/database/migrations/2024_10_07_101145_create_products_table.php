<?php

use App\Models\Currency;
use App\Models\Product;
use App\Models\ProductAccountItem;
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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('名前');
            $table->string('logo_path')->comment('ロゴ');
            $table->string('unit')->comment('単位');
            $table->unsignedTinyInteger('scale')->comment('小数桁数');
            $table->foreignIdFor(Currency::class)->constrained()->comment('通貨ID');
            $table->double('base_currency_rate')->comment('ベース通貨レート');
            $table->double('trade_fee')->comment('トレード手数料');
            $table->double('refund_fee')->comment('返金手数料');
            $table->timestamps();
            $table->softDeletes()->comment('削除日時');
            $table->comment('商品');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
