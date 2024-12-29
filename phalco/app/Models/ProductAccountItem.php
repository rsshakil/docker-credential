<?php

namespace App\Models;

use App\Enums\ProductAccountItemType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductAccountItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'type' => ProductAccountItemType::class,
    ];

    /**
     * Relation
     */
    
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
      * 商品送付先の項目のオプション定義
      *
      * @return \Illuminate\Database\Eloquent\Relations\HasMany
      */
    public function productAccountItemOptions()
    {
        return $this->hasMany(ProductAccountItemOption::class);
    }
}
