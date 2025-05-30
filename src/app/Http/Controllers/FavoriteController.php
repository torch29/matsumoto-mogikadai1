<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Item;

class FavoriteController extends Controller
{
    public function isFavorite($item_id)
    {
        $user = Auth::user();
        return $user->favoriteItems()->where('item_id', $item_id)->exists();
    }

    public function favorite($item_id)
    {
        $user = Auth::user();
        $item = Item::find($item_id);

        if ($item->user_id === $user->id) {
            redirect("/item/{$item->id}");
        }

        if (!$user->favoriteItems()->where('item_id', $item_id)->exists()) {
            $user->favoriteItems()->syncWithoutDetaching($item_id);
        }

        return back();
    }

    public function removeFavorite($item_id)
    {
        $user = Auth::user();
        if ($user->favoriteItems()->where('item_id', $item_id)->exists()) {
            $user->favoriteItems()->detach($item_id);
        }
        return back();
    }
}
