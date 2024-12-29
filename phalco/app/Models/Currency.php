<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;

    protected $guarded = [
        'created_at',
        'updated_at',
    ];

    /**
     * Relation
     */

    /**
     * レート一覧
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function currencyRates()
    {
        return $this->belongsToMany(self::class, 'currency_rates', 'base_id', 'target_id')
            ->withPivot(['rate', 'fixed_rate'])
            ->withTimestamps();
    }

    /**
     * 支払方法
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function paymentMethods()
    {
        return $this->hasMany(PaymentMethod::class);
    }
}
