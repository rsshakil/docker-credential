<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    protected $appends = [
        'logo_url',
    ];

    /**
     * ロゴURL
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
     * ベース通貨
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    /**
    * 商品送付先の項目設定
    *
    * @return \Illuminate\Database\Eloquent\Relations\HasMany
    */
    public function productAccountItems()
    {
        return $this->hasMany(ProductAccountItem::class);
    }

    /**
     * 運営アカウント
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function adminProductAccounts()
    {
        return $this->hasMany(AdminProductAccount::class);
    }

    /**
     * 商品送付先
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userProductAccounts()
    {
        return $this->hasMany(UserProductAccount::class);
    }
}
