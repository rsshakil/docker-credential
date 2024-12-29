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
        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('名前');
            $table->string('detail')->comment('内容');
            $table->string('category')->comment('カテゴリ');
            $table->timestamps();
            $table->softDeletes()->comment('削除日時');
            $table->comment('お知らせ');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faqs');
    }
};
