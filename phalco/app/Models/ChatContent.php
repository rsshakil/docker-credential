<?php

namespace App\Models;

use App\Enums\ChatContentType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatContent extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'chat_room_user_id',
        'content_type',
        'content'
    ];

    protected $casts = [
        'content_type' => ChatContentType::class,
    ];
    
    /**
     * chatRoomUser
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function chatRoomUser()
    {
        return $this->belongsTo(ChatRoomUser::class);
    }
}
