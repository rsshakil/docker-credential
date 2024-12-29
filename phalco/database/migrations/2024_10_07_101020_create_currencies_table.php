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
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('名称');
            $table->string('code')->comment('コード');
            $table->unsignedTinyInteger('scale')->comment('小数桁数');
            $table->double('min_amount')->comment('最小取引額');
            $table->datetimes();
            $table->softDeletes()->comment('削除日時');
            $table->comment('通貨');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
