<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    public function categorizedItems()
    {
        return $this->belongsToMany(Item::class)->withTimestamps();
    }

    protected $fillable = ['content'];
}
