<?php

namespace App\Models;

use App\Enums\PaymentItemType;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SellerPaymentItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
      'seller_payment_id',
      'payment_item_id',
      'value',
    ];

    protected $appends = [
        'value_url',
    ];

    /**
     * 設定値が画像の場合URL
     *
     * @return Attribute
     */
    public function valueUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->paymentItem->type === PaymentItemType::IMAGE && filled($this->value)) {
                    return Storage::url($this->value);
                }
                return null;
            }
        );
    }

    /**
     * Relation
     */

    /**
     * paymentItems 支払方法の項目定義
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function paymentItem()
    {
        return $this->belongsTo(PaymentItem::class);
    }

    /**
     * オプション設定値
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function paymentOptions()
    {
        return $this->belongsToMany(PaymentOption::class, 'seller_payment_options');
    }
}
