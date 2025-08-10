<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    const PAYMENTS = [
        'konbini' => 'コンビニ払い',
        'card' => 'カード支払い'
    ];

    public static function getPayments()
    {
        return self::PAYMENTS;
    }

    public function getSelectedPayment()
    {
        return self::PAYMENTS[$this->payment];
    }

    protected $fillable = [
        'item_id',
        'user_id',
        'payment',
        'zip_code',
        'address',
        'building',
        'status'
    ];

    public function purchasedUser()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function purchasedItem()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class, 'purchase_id');
    }

    public function chats()
    {
        return $this->hasMany(Chat::class);
    }
}
