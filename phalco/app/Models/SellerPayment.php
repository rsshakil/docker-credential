<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SellerPayment extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'payment_method_id',
        'is_active',
      ];

    /**
     * Relation
     */

    /**
     * paymentMethod 支払方法
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    /**
      * sellerPaymentItem 支払先情報の項目
      *
      * @return \Illuminate\Database\Eloquent\Relations\HasMany
      */
    public function sellerPaymentItems()
    {
        return $this->hasMany(SellerPaymentItem::class);
    }
}
