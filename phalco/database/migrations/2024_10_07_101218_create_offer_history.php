<?php

use App\Models\Offer;
use App\Models\OfferHistory;
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
        Schema::create('offer_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Offer::class)->constrained()->comment('オファーID');
            $table->morphs('operator');
            $table->foreignIdFor(AdminProductAccount::class)->nullable()->constrained()->comment('運営アカウントID');
            $table->unsignedTinyInteger('result_status')->comment('操作後オファーステータス');
            $table->string('reason')->nullable()->comment('理由');
            $table->timestamps();
            $table->comment('オファー操作履歴');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offer_histories');
    }
};