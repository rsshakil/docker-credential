<?php

namespace App\Models;

use App\Enums\PaymentItemType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentItem extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'type' => PaymentItemType::class,
    ];

    /**
     * Relation
     */

    /**
     * paymentOption 支払方法の項目のオプション定義
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function paymentOptions()
    {
        return $this->hasMany(PaymentOption::class);
    }
}
