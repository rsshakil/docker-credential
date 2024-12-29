<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductAccountItemOption extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    /**
     * 商品送付先の項目定義
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function productAccountItem()
    {
        return $this->belongsTo(ProductAccountItem::class);
    }
}
