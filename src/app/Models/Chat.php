<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
