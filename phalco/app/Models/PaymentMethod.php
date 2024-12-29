<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class PaymentMethod extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    protected $appends = [
        'logo_url',
    ];

    /**
     * 掲載額
     *
     * @return Attribute
     */
    public function logoUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (filled($this->logo_path)) {
                    return Storage::url($this->logo_path);
                }
                return null;
            }
        );
    }

    /**
     * Relation
     */

    /**
     * currency 通貨
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currency()
    {
        return  $this->belongsTo(Currency::class, 'currency_id');
    }

    /**
     * paymentItems 支払方法の項目定義
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function paymentItems()
    {
        return $this->hasMany(PaymentItem::class);
    }
}
