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
        Schema::create('announcement_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('名前');
            $table->boolean('has_thumbnail')->comment('サムネイル表示');
            $table->timestamps();
            $table->softDeletes()->comment('削除日時');
            $table->comment('お知らせカテゴリ');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcement_categories');
    }
};
