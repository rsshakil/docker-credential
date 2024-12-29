<?php

use App\Models\Offer;
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
        Schema::create('offer_requirements', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Offer::class)->constrained()->comment('オファーID');
            $table->unsignedInteger('traded_times')->comment('トレード数');
            $table->double('paid_period')->comment('平均支払時間');
            $table->double('comfirmed_period')->comment('平均リリース時間');
            $table->double('trade_accuracy')->comment('トレード精度');
            $table->unsignedInteger('block_times')->comment('被ブロック数');
            $table->timestamps();
            $table->comment('トレード条件');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offer_requirements');
    }
};