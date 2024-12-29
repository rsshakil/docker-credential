<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KycApplication extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'administrator_id',
        'rejected_reason',
      ];

    /**
     * Relation
     */

    /**
     * 管理者
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function approvedBy()
    {
        return $this->belongsTo(Administrator::class);
    }

    /**
     * KYC画像
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function kycImages()
    {
        return $this->hasMany(KycImage::class);
    }
}
