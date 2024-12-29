<?php

use App\Models\ProductAccountItemOption;
use App\Models\UserProductAccountItem;
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
        Schema::create('user_product_account_item_options', function (Blueprint $table) {
            $table->foreignIdFor(UserProductAccountItem::class)->constrained(indexName: 'upaio_upai_id_foreign')->comment('商品送付先の項目ID');
            $table->foreignIdFor(ProductAccountItemOption::class)->constrained(indexName: 'upaio_paio_id_foreign')->comment('商品送付先の項目のオプション定義ID');
            $table->comment('商品送付先のオプション設定値');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_product_account_item_options');
    }
};
