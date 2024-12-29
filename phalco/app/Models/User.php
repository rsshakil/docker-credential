<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enums\KycStatus;
use App\Enums\Language;
use App\Enums\UserStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'kyc_status',
        'language',
        'full_name',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'language' => Language::class,
        'user_status' => UserStatus::class,
        'kyc_status' => KycStatus::class,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'kyc_status' => KycStatus::class,
            'user_status' => UserStatus::class,
        ];
    }

    /**
     * Relation
     */

    /**
     * 実績
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function recentAchievement()
    {
        return $this->hasOne(RecentAchievement::class);
    }

    /**
     * KYC情報
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function kycApplications()
    {
        return $this->hasMany(KycApplication::class);
    }

    /**
     * 最新のKYC情報
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function kycApplication()
    {
        return $this->kycApplications()->one()->latestOfMany();
    }

    /**
     * 最新のKYC却下情報
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function kycApplicationRejectedReason()
    {
        return $this->kycApplications()->one()->ofMany([
            'created_at' => 'max',
        ], function (Builder $query) {
            $query->whereNot( 'rejected_reason', '');
        });
    }

    /**
     * ブロックユーザー
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function blockUsers()
    {
        return $this->hasMany(BlockUser::class);
    }

    /**
     * 支払情報
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function buyerPayments()
    {
        return $this->hasMany(BuyerPayment::class);
    }

    /**
     * 支払先情報
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sellerPayments()
    {
        return $this->hasMany(SellerPayment::class);
    }

    /**
     * オファー
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function offers()
    {
        return $this->hasMany(Offer::class);
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
