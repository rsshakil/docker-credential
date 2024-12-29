<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdminProductAccount extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'is_temporary' => 'boolean',
        'is_send' => 'boolean',
    ];

    /**
     * 商品
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * 運営アカウントの項目
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function adminProductAccountItems()
    {
        return $this->hasMany(AdminProductAccountItem::class);
    }
}
