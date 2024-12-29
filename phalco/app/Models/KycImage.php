<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class KycImage extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
      'kyc_application_id',
      'image_path',
    ];

    protected $appends = [
        'image_url',
    ];

    /**
     * 画像URL
     *
     * @return Attribute
     */
    public function imageUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (filled($this->image_path)) {
                    return Storage::url($this->image_path);
                }
                return null;
            }
        );
    }
}
