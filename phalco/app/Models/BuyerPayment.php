<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class BuyerPayment extends Pivot
{
    use HasFactory, SoftDeletes;

    protected $table = 'buyer_payments';

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
}
