<?php

use App\Models\ProductAccountItem;
use App\Models\AdminProductAccount;
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
        Schema::create('admin_product_account_items', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(AdminProductAccount::class)->constrained()->comment('運営アカウントID');
            $table->foreignIdFor(ProductAccountItem::class)->constrained()->comment('商品送付先の項目定義ID');
            $table->string('value')->nullable()->comment('設定値');
            $table->timestamps();
            $table->comment('運営アカウントの項目');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_product_account_items');
    }
};
