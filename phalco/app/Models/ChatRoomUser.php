<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphPivot;

class ChatRoomUser extends MorphPivot
{
    use HasFactory;

    protected $table = 'chat_room_users';
    public $incrementing = true;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'chat_room_id', 
        'participant_type', 
        'participant_id', 
        'read_content',
    ];
    
    /**
     * Relation
     */

    /**
     * participant
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function participant()
    {
        return $this->morphTo();
    }

    /**
     * chatContents チャットコンテンツ
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function chatContents()
    {
        return $this->hasMany(ChatContent::class);
    }
}
