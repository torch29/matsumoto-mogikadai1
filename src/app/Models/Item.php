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
        return $this->belongsTo(User::class, 'user_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function favoriteUsers()
    {
        return $this->belongsToMany(User::class, 'favorites', 'item_id', 'user_id')->withTimestamps();
    }

    public static function getConditionLabels()
    {
        return self::CONDITION_LABELS;
    }

    public function getSelectedCondition()
    {
        return self::CONDITION_LABELS[$this->condition];
    }

    public function scopeNameSearch($query, $word)
    {
        if (!empty($word)) {
            $query->where('name', 'like', '%' . $word . '%');
        }
    }

    protected $fillable = [
        'user_id',
        'name',
        'brand_name',
        'price',
        'explain',
        'condition',
        'img_path',
        'status'
    ];
}
