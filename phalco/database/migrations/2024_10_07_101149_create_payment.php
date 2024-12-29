<?php

use App\Models\Currency;
use App\Models\PaymentMethod;
use App\Models\PaymentItem;
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
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Currency::class)->constrained()->comment('通貨ID');
            $table->string('name')->comment('名前');
            $table->string('logo_path')->comment('ロゴ');
            $table->timestamps();
            $table->softDeletes()->comment('削除日時');
            $table->comment('支払方法');
        });
        Schema::create('payment_items', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(PaymentMethod::class)->constrained()->comment('支払方法ID');
            $table->unsignedTinyInteger('type')->comment('項目タイプ');
            $table->string('name')->comment('項目名');
            $table->timestamps();
            $table->softDeletes()->comment('削除日時');
            $table->comment('支払方法の項目定義');
        });
        Schema::create('payment_options', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(PaymentItem::class)->constrained()->comment('支払方法の項目定義ID');
            $table->string('name')->comment('項目名');
            $table->timestamps();
            $table->softDeletes()->comment('削除日時');
            $table->comment('支払方法の項目のオプション定義');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
        Schema::dropIfExists('payment_items');
        Schema::dropIfExists('payment_options');
    }
};
