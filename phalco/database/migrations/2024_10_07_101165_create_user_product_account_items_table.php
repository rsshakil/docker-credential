<?php

use App\Models\ProductAccountItem;
use App\Models\UserProductAccount;
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
        Schema::create('user_product_account_items', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(UserProductAccount::class)->constrained()->comment('商品送付先ID');
            $table->foreignIdFor(ProductAccountItem::class)->constrained()->comment('商品送付先の項目定義ID');
            $table->string('value')->nullable()->comment('設定値');
            $table->timestamps();
            $table->comment('商品送付先の項目');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_product_account_items');
    }
};
