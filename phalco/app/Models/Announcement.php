<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Announcement extends Model
{
    use HasFactory, SoftDeletes;

    protected $appends = [
        'thumbnail_url',
    ];

    /**
     * Relations
     */

    /**
     * カテゴリ
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function announcementCategory()
    {
        return $this->belongsTo(AnnouncementCategory::class);
    }

    /**
     * サムネイルURL
     *
     * @return Attribute
     */
    public function thumbnailUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (filled($this->thumbnail)) {
                    return Storage::url($this->thumbnail);
                }
                return null;
            }
        );
    }

}
