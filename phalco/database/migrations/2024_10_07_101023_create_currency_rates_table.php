<?php

use App\Models\Currency;
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
        Schema::create('currency_rates', function (Blueprint $table) {
            $table->foreignIdFor(Currency::class, 'base_id')->constrained('currencies')->comment('ベース通貨ID');
            $table->foreignIdFor(Currency::class, 'target_id')->constrained('currencies')->comment('対象通貨ID');
            $table->double('rate')->comment('レート');
            $table->double('fixed_rate')->nullable()->comment('固定レート');
            $table->timestamps();
            $table->comment('通貨レート');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currency_rates');
    }
};
