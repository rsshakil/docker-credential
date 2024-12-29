<?php

use App\Models\Product;
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
        Schema::create('product_account_items', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Product::class)->constrained()->comment('商品ID');
            $table->unsignedTinyInteger('type')->comment('項目種類');
            $table->string('name')->comment('項目名');
            $table->timestamps();
            $table->softDeletes()->comment('削除日時');
            $table->comment('商品送付先の項目定義');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_account_items');
    }
};
