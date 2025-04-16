<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    public function categories()
    {
        return $this->belongsToMany(Category::class)->withTimestamps();
    }

    public function users()
    {
        return $this->belongsTo(User::class);
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
