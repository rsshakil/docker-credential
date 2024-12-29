<?php

use App\Models\ChatRoom;
use App\Models\ChatRoomUser;
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
        Schema::create('chat_rooms', function (Blueprint $table) {
            $table->id();
            $table->morphs('chat_holder');
            $table->unsignedTinyInteger('room_type')->comment('ルーム種類');
            $table->timestamps();
            $table->comment('チャットルーム');
        });

        Schema::create('chat_room_users', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(ChatRoom::class)->constrained()->comment('チャットルームID');
            $table->morphs('participant');
            $table->integer('read_content')->nullable()->comment('最後に読んだ内容');
            $table->timestamps();
            $table->comment('チャットルームユーザー');
        });

        Schema::create('chat_contents', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(ChatRoomUser::class, 'chat_room_user_id')->constrained()->comment('チャットルームユーザーID');
            $table->unsignedTinyInteger('content_type')->comment('内容種類');
            $table->string('content')->comment('内容');
            $table->timestamps();
            $table->comment('チャットコンテンツ');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_rooms');
        Schema::dropIfExists('chat_room_users');
        Schema::dropIfExists('chat_contents');
    }
};