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
        } else {
            $profile = '';
        }

        return view('user.profile', compact('profile', 'user'));
    }

    public function updateProfile(Request $request)
    {
        $user = User::find($request->user_id);
        $user->update($request->only('name'));

        $profiles = $request->only(['zip_code', 'address', 'building']);

        $profile = $user->profile;
        if (!$profile) {
            $profile = $user->profile()->create($profiles);
        } else {
            $profile->update($profiles);
        }

        if ($request->hasFile('profile_img')) {
            $file = $request->file('profile_img');
            $fileName = $user->id . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/img/user/', $fileName);

            $profileImgPath = 'storage/img/user/' . $fileName;
            $profile->update(['profile_img' => $profileImgPath]);
        }
        /*
        //↓の１行目を$user = Auth::user();にしたいけどそうすると一部赤く表示されちゃう。動きには問題なさそうだけど

        $user = User::find($request->user_id);
        $user->update($request->only('name'));

        $profile = $user->profile;
        $profiles = $request->only(['zip_code', 'address', 'building']);
        if ($profile) {
            $profile->update($profiles);
        } else {
            $profile->create($profiles);
        }

        if ($request->hasFile('profile_img')) {
            $file = $request->file('profile_img');
            $fileName = $user->id . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/img/user/', $fileName);

            $profileImgPath = 'storage/img/user/' . $fileName;

            if ($profile) {
                $profile->update([
                    'profile_img' => $profileImgPath
                ]);
            } else {
                $user->profile()->create([
                    'profile_img' => $profileImgPath
                ]);
            }
        }
            */

        return redirect('/mypage/profile');
    }

    public function showMypage()
    {
        $user = Auth::user();
        $sellItems = $user->items;
        $purchasedItems = $user->purchases()->with('purchasedItem')->get();

        return view('user.mypage', compact('user', 'sellItems', 'purchasedItems'));
    }
}
