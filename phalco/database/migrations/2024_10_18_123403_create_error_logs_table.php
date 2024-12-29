<?php

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
        Schema::create('error_logs', function (Blueprint $table) {
            $table->id();
            $table->string('file')->comment('ファイル名');
            $table->unsignedInteger('line_no')->comment('行番号');
            $table->string('error_code')->comment('エラーコード');
            $table->text('message')->comment('エラーメッセージ');
            $table->date('date')->comment('発生日');
            $table->unsignedInteger('count')->default(0)->comment('発生回数');
            $table->timestamps();
            $table->comment('エラーログ');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('error_logs');
    }
};
