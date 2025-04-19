<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\User;
use App\Models\Profile;

class UserController extends Controller
{
    public function profile()
    {
        $user = Auth::user();

        if ($user->profile) {
            $profile = $user->profile;
        }

        return view('auth.profile', compact('profile', 'user'));
    }

    public function updateProfile(Request $request)
    {
        $user = User::find($request->user_id);
        $user->update($request->only('name'));

        $profile = $user->profile;
        $profiles = $request->only(['zip_code', 'address', 'building']);
        if ($profile) {
            $profile->update($profiles);
        } else {
            $user->profile()->create(
                $profiles
            );
        }

        return redirect('/mypage/profile');
    }

    public function mypage()
    {
        $items = Item::with('users')->get();

        return view('auth.mypage', compact('items'));
    }
}
