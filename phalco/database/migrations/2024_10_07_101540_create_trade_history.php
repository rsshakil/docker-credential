<?php

use App\Models\AdminProductAccount;
use App\Models\Trade;
use App\Models\TradeHistory;
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
        Schema::create('trade_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Trade::class)->constrained()->comment('トレードID');
            $table->morphs('operator');
            $table->foreignIdFor(AdminProductAccount::class)->nullable()->constrained()->comment('運営アカウントID');
            $table->unsignedTinyInteger('result_status')->comment('操作後トレードステータス');
            $table->string('reason')->nullable()->comment('理由');
            $table->timestamps();
            $table->comment('トレード操作履歴');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trade_histories');
    }
};