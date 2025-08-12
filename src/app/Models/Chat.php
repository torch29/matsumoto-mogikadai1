<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Chat extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_id',
        'sender_id',
        'message',
        'img_path',
    ];

    public function tradingPurchaseItem()
    {
        return $this->belongsTo(Purchase::class, 'purchase_id');
    }

    public function sendUser()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /*
    public function purchasedUserRead()
    {
        return $this->hasOne(PurchaseUserRead::class, 'purchase_id', 'purchase_id');
    }

    /* 取引チャットの未読件数
    public static function unreadCountsForUser($userId, $purchaseIds)
    {
        return self::whereIn('purchase_id', $purchaseIds)
            ->where('sender_id', '<>', $userId)
            ->whereHas('purchaseUserRead', function ($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->whereColumn('chats.purchase_id', 'purchase_user_reads.purchase_id')
                    ->where(function ($q) {
                        $q->whereNull('last_read_at')
                            ->orWhereColumn('chats.created_at', '>', 'purchase_user_reads.last_read_at');
                    });
            })
            ->select('purchase_id', DB::raw('COUNT(*) as unread_count'))
            ->groupBy('purchase_id')
            ->pluck('unread_count', 'purchase_id');
    }
            */
}
