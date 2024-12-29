<?php

use App\Models\AnnouncementCategory;
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
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(AnnouncementCategory::class)->nullable()->constrained()->comment('お知らせカテゴリID');
            $table->string('name')->comment('名前');
            $table->longText('detail')->comment('内容');
            $table->dateTime('start_at')->comment('掲載開始日時');
            $table->dateTime('end_at')->comment('掲載日時終了');
            $table->string('thumbnail')->nullable()->comment('サムネイルURL');
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
        Schema::dropIfExists('announcements');
    }
};
