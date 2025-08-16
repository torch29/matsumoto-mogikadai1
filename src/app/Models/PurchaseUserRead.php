<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PurchaseUserRead extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_id',
        'user_id',
        'last_read_at',
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'purchase_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function unreadCountsForUser($userId, $purchaseIds)
    {
        return Chat::select('purchase_id', DB::raw('count(*) as unread_count'))
            ->whereIn('purchase_id', $purchaseIds)
            ->where('sender_id', '<>', $userId)
            ->where(function ($query) use ($userId) {
                $query->whereExists(function ($sub) use ($userId) {
                    $sub->select(DB::raw(1))
                        ->from('purchase_user_reads')
                        ->whereColumn('purchase_user_reads.purchase_id', 'chats.purchase_id')
                        ->where('purchase_user_reads.user_id', $userId)
                        ->whereColumn('purchase_user_reads.last_read_at', '<', 'chats.created_at');
                });
            })
            ->groupBy('purchase_id')
            ->pluck('unread_count', 'purchase_id');
    }
}
