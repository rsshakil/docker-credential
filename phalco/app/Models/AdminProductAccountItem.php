<?php

namespace App\Models;

use App\Enums\ProductAccountItemType;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class AdminProductAccountItem extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
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
                if ($this->productAccountItem->type === ProductAccountItemType::IMAGE && filled($this->value)) {
                    return Storage::url($this->value);
                }
                return null;
            }
        );
    }

    /**
     * 運営アカウント
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function adminProductAccount()
    {
        return $this->belongsTo(AdminProductAccount::class);
    }

    /**
     * 商品送付先の項目定義
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function productAccountItem()
    {
        return $this->belongsTo(ProductAccountItem::class);
    }

    /**
     * オプション設定値
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function productAccountItemOptions()
    {
        return $this->belongsToMany(ProductAccountItemOption::class, 'admin_product_account_item_options');
    }
}
