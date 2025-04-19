<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Profile;

class UserController extends Controller
{
    public function profile()
    {
        $profile = Profile::all();
        return view('auth.profile', compact('profile'));
    }

    public function mypage()
    {
        $items = Item::with('users')->get();

        return view('auth.mypage', compact('items'));
    }
}
