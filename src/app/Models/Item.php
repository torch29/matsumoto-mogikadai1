<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    const CONDITION_LABELS = [
        1 => '良好',
        2 => '目立った傷や汚れなし',
        3 => 'やや傷や汚れあり',
        4 => '状態が悪い'
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class)->withTimestamps();
    }

    public function users()
    {
        return $this->belongsTo(User::class);
    }

    public static function getConditionLabels()
    {
        return self::CONDITION_LABELS;
    }

    public function getSelectedCondition()
    {
        return self::CONDITION_LABELS[$this->condition];
    }

    protected $fillable = [
        'user_id',
        'name',
        'brand_name',
        'price',
        'explain',
        'condition',
        'img_path'
    ];
}
