<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Item;
use App\Models\User;
use App\Models\Purchase;
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

    public function showMypage(Request $request)
    {
        // もし購入処理セッションを持った状態なら、テーブルへの保存と更新の処理
        if (session()->has('purchased_item_id')) {
            try {
                DB::beginTransaction();

                $itemId = session('purchased_item_id');
                $payment = session('purchased_payment');
                $address = session('purchased_address');

                Purchase::create([
                    'user_id' => Auth::id(),
                    'item_id' => $itemId,
                    'payment' => $payment,
                    'zip_code' => $address['zip_code'],
                    'address' => $address['address'],
                    'building' => $address['building']
                ]);

                Item::find($itemId)->update(['status' => 'sold']);

                session()->forget(['purchased_item_id', 'purchased_payment', 'purchased_address']);

                session()->forget("addressData_{$itemId}");

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('detail内の決済後処理エラー：' . $e->getMessage());
                // dd($e->getMessage());
            }
        }

        $user = Auth::user();
        $sellItems = $user->items;
        $purchasedItems = $user->purchases()->with('purchasedItem')->get();

        $konbiniCheckoutUrl = session('konbini_checkout_url');
        session()->forget('konbini_checkout_url');

        return view('user.mypage', compact('user', 'sellItems', 'purchasedItems', 'konbiniCheckoutUrl'));
    }
}
