<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Item;
use App\Models\Purchase;
use App\Http\Requests\PurchaseRequest;
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

        $address = session()->get('addressData', [
            'zip_code' => $profile->zip_code,
            'address' => $profile->address,
            'building' => $profile->building
        ]);

        return view('purchase', compact('item', 'payments', 'profile', 'address'));
    }

    public function changeAddress($id)
    {
        $item = Item::find($id);
        return view('change_address', compact('item'));
    }

    public function saveShippingAddress(Request $request, $id)
    {
        $addressData = $request->only(['zip_code', 'address', 'building']);
        $request->session()->put('addressData', $addressData);

        return redirect('purchase/' . $id);
    }

    //購入を決定し、stripe checkoutへ遷移する
    public function decidePurchase(PurchaseRequest $request, $itemId)
    {
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        $item = Item::findOrFail($itemId);
        $payment = $request->input('payment');

        //セッションに保存する
        $user = Auth::user();
        $address = $request->session()->get('addressData', [
            'zip_code' => $user->profile->zip_code,
            'address' => $user->profile->address,
            'building' => $user->profile->building
        ]);

        //stripe checkoutの処理
        $session = Session::create([
            'payment_method_types' => [$payment],  //'card', 'konbini'から変更
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
            'success_url' => route('purchase.success'),
            'cancel_url' => route('purchase.cancel'),
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
            return view('purchase.konbini', ['checkoutUrl' => $session->url]);
        }

        session([
            'purchased_item_id' => $item->id,
            'purchased_payment' => $payment,
            'purchased_address' => $address
        ]);

        //カード支払いを選択した場合のリダイレクト
        return redirect($session->url);
    }

    //stripe checkoutで決済完了後に表示
    public function success(Request $request)
    {
        //セッションから取得する
        $itemId = session('purchased_item_id');
        $payment = session('purchased_payment');
        $address = session('purchased_address');

        if (!$itemId || !$payment || !$address) {
            return redirect('/')->with('error', '購入情報が見つかりませんでした');
        }

        try {
            DB::beginTransaction();

            if ($payment === 'card') {

                Purchase::create([
                    'user_id' => Auth::id(),
                    'item_id' => $itemId,
                    'payment' => $payment,
                    'zip_code' => $address['zip_code'],
                    'address' => $address['address'],
                    'building' => $address['building']
                ]);

                Item::find($itemId)->update(['status' => "sold"]);
            }

            session()->forget([
                'purchased_item_id',
                'purchased_payment',
                'purchased_address'
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('決済後の保存処理中にエラー：' . $e->getMessage());
            return redirect('/')->with('error', '購入情報の保存に失敗しました');
        }

        return view('purchase.success');
    }

    public function cancel()
    {
        return view('purchase.cancel');
    }
}
