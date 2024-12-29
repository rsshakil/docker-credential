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
        Schema::create('admin_product_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Product::class)->constrained()->comment('商品ID');
            $table->string('name')->comment('名前');
            $table->boolean('is_temporary')->comment('一時預り用');
            $table->boolean('is_send')->comment('送付用');
            $table->softDeletes();
            $table->timestamps();
            $table->comment('運営アカウント');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_product_accounts');
    }
};
