<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    const PAYMENTS = [
        1 => 'コンビニ払い',
        2 => 'カード支払い'
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
}
