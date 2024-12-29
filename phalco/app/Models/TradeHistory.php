<?php

namespace App\Models;

use App\Enums\TradeStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TradeHistory extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'result_status' => TradeStatus::class,
    ];

    /**
     * Relation
     */

    /**
     * 操作ユーザー
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function operator()
    {
        return $this->morphTo();
    }

    /**
     * オファー操作履歴運営アカウント
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function adminProductAccount()
    {
        return $this->belongsTo(AdminProductAccount::class);
    }
}