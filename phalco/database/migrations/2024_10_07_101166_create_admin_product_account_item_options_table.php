<?php

use App\Models\AdminProductAccountItem;
use App\Models\ProductAccountItemOption;
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
        Schema::create('admin_product_account_item_options', function (Blueprint $table) {
            $table->foreignIdFor(AdminProductAccountItem::class)->constrained(indexName: 'apaio_apai_id_foreign')->comment('運営アカウントの項目ID');
            $table->foreignIdFor(ProductAccountItemOption::class)->constrained(indexName: 'apaio_paio_id_foreign')->comment('商品送付先の項目のオプション定義ID');
            $table->comment('運営アカウントのオプション設定値');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_product_account_item_options');
    }
};
