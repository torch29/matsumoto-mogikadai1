<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function Items()
    {
        return $this->hasMany(Item::class);
    }

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function favoriteItems()
    {
        return $this->belongsToMany(Item::class, 'favorites', 'user_id', 'item_id')->withTimestamps();
    }

    public function tradingChats()
    {
        return $this->hasMany(Chat::class, 'user_id');
    }

    public function purchaseReads()
    {
        return $this->hasMany(PurchaseUserRead::class);
    }

    public function receivedRatings()
    {
        return $this->hasMany(Rating::class, 'reviewee_id');
    }

    /* 取引中の商品リストを最新チャット順に並び替える */
    public function tradingItems()
    {
        //自分が出品して売れた商品（取引中＆購入者のみ評価済み）
        $sellTradingItems = $this->items()
            ->whereHas('purchases', fn($q) => $q->whereIn('status', ['trading', 'buyer_rated']))
            ->with(['purchases.chats' => fn($q) => $q->orderByDesc('created_at')->limit(1)])
            ->get();

        //購入した商品（取引中）
        $purchasedTradingItems = $this->purchases()
            ->where('status', 'trading')
            ->with(['purchasedItem.purchases.chats' => fn($q) => $q->orderByDesc('created_at')->limit(1)])
            ->get()
            ->map(fn($purchase) => $purchase->purchasedItem);

        // 取引中の商品をマージして、最新チャット順にソートする
        return $sellTradingItems
            ->merge($purchasedTradingItems)
            ->sortByDesc(function ($item) {
                $purchase = $item->purchases->first();
                $latestChatDate = optional($purchase?->chats->first())->created_at;
                return $latestChatDate ?? $purchase?->created_at;
            })
            ->values();
    }
}
