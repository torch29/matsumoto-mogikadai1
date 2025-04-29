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
        'building'
    ];

    public function purchasedUser()
    {
        return $this->belongsTo(User::class);
    }

    public function purchasedItem()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
}
