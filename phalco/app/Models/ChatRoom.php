<?php

namespace App\Models;

use App\Enums\RoomType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatRoom extends Model
{
    use HasFactory;

       /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'chat_holder_type', 
        'chat_holder_id', 
        'room_type',
    ];

    protected $casts = [
        'room_type' => RoomType::class,
    ];

    /**
     * Relation
     */

    /**
     * チャットルーム参加者（ユーザー）
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function users()
    {
        return $this->morphedByMany(User::class, 'participant', 'chat_room_users')
            ->using(ChatRoomUser::class)
            ->withPivot(['id', 'chat_room_id', 'read_content']);
    }

    /**
     * チャットルーム参加者（アドミン）
     * 
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function admins()
    {
        return $this->morphedByMany(Administrator::class, 'participant', 'chat_room_users')
            ->using(ChatRoomUser::class)
            ->withPivot(['id', 'chat_room_id', 'read_content']);
    }

    /**
     * chatRoomContents
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function contents()
    {
        return $this->hasManyThrough(ChatContent::class, ChatRoomUser::class,'chat_room_id', 'chat_room_user_id', 'id', 'id');
    }
}
