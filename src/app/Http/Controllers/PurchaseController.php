<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Item;
use App\Models\Purchase;
use App\Http\Requests\PurchaseRequest;
use App\Http\Requests\AddressRequest;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class PurchaseController extends Controller
{
    public function purchase($id)
    {
        $item = Item::find($id);
        $payments = Purchase::getPayments();
        $user = Auth::user();
        $profile = $user->profile;

        $address = session()->get("addressData_{$id}", [
            'zip_code' => optional($profile)->zip_code,
            'address' => optional($profile)->address,
            'building' => optional($profile)->building
        ]);

        return view('item.purchase.checkout', compact('item', 'payments', 'profile', 'address'));
    }

    public function changeAddress($id)
    {
        $item = Item::find($id);
        return view('item.purchase.change_address', compact('item'));
    }

    public function saveShippingAddress(AddressRequest $request, $id)
    {
        $addressData = $request->only(['zip_code', 'address', 'building']);
        $request->session()->put("addressData_{$id}", $addressData);

        return redirect('purchase/' . $id);
    }

    //購入を決定し、stripe checkoutへ遷移する
    public function decidePurchase(PurchaseRequest $request, $itemId)
    {
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        $item = Item::find($itemId);
        $user = Auth::user();

        if ($item->user_id === $user->id) {
            return redirect("/item/{$item->id}")->with('error', '自身が出品した商品です');
        }
        if ($item->status !== 'available') {
            return redirect("/item/{$item->id}")->with('error', 'この商品は売り切れです');
        }

        $payment = $request->input('payment');

        //セッションに保存する
        $address = $request->session()->get("addressData_{$itemId}", [
            'zip_code' => $user->profile->zip_code,
            'address' => $user->profile->address,
            'building' => $user->profile->building
        ]);

        //stripe checkoutの処理
        $session = Session::create([
            'payment_method_types' => [$payment],  //プルダウンで選択された 'card', 'konbini'を送信
            'line_items' => [[
                'price_data' => [
                    'currency' => 'jpy',
                    'product_data' => [
                        'name' => $item->name,
                    ],
                    'unit_amount' => $item->price,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => url('/mypage?tab=buy'),
            'cancel_url' => url("/item/{$item->id}"),
        ]);

        if ($payment === 'konbini') {
            DB::transaction(function () use ($item, $user, $address, $payment) {
                Purchase::create([
                    'item_id' => $item->id,
                    'user_id' => $user->id,
                    'payment' => $payment,
                    'zip_code' => $address['zip_code'],
                    'address' => $address['address'],
                    'building' => $address['building'],
                ]);

                $item->update(['status' => 'pending']);
            });

            session([
                'konbini_checkout_url' => $session->url
            ]);

            return redirect('/mypage?tab=buy');
        }

        session([
            'purchased_item_id' => $item->id,
            'purchased_payment' => $payment,
            'purchased_address' => $address
        ]);

        //カード支払いを選択した場合のリダイレクト
        return redirect($session->url);
    }

    public function cancel()
    {
        return view('purchase.cancel');
    }
}
