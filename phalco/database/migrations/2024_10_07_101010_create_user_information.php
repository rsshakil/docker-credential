<?php

use App\Models\Administrator;
use App\Models\KycApplication;
use App\Models\User;
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
        Schema::create('block_users', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class, 'user_id')->constrained('users')->comment('ユーザーID');
            $table->foreignIdFor(User::class, 'block_user_id')->constrained('users')->comment('ブロックユーザーID');
            $table->timestamps();
            $table->comment('ブロックユーザー管理');
        });
        Schema::create('kyc_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->comment('ユーザーID');
            $table->foreignIdFor(Administrator::class)->nullable()->constrained()->comment('管理者ID');
            $table->string('rejected_reason')->nullable()->comment('却下理由');
            $table->timestamps();
            $table->comment('KYC情報');
        });
        Schema::create('kyc_images', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(KycApplication::class)->constrained()->comment('KYC情報ID');
            $table->string('image_path')->comment('KYC画像');
            $table->timestamps();
            $table->comment('KYC画像');
        });
        Schema::create('recent_achievements', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->comment('ユーザーID');
            $table->unsignedInteger('traded_times')->nullable()->comment('トレード数');
            $table->double('paid_period')->nullable()->comment('平均支払時間');
            $table->double('comfirmed_period')->nullable()->comment('平均リリース時間');
            $table->double('trade_accuracy')->nullable()->comment('トレード精度');
            $table->unsignedInteger('block_times')->nullable()->comment('被ブロック数');
            $table->timestamps();
            $table->comment('実績');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('block_users');
        Schema::dropIfExists('kyc_applications');
        Schema::dropIfExists('kyc_images');
        Schema::dropIfExists('recent_achievements');
    }
};
