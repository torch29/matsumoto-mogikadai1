<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Item;
use App\Models\Purchase;

class PurchaseController extends Controller
{
    public function index($id)
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

    public function decidePurchase(Request $request)
    {
        try {
            DB::beginTransaction();

            $user = Auth::user();
            $address = $request->session()->get('addressData', [
                'zip_code' => $user->profile->zip_code,
                'address' => $user->profile->address,
                'building' => $user->profile->building
            ]);
            $purchaseData = [
                'item_id' => $request->item_id,
                'user_id' => $user->id,
                'payment' => $request->payment,
                'zip_code' => $address['zip_code'],
                'address' => $address['address'],
                'building' => $address['building']
            ];
            Purchase::create($purchaseData);

            Item::find($request->item_id)->update(['status' => "sold"]);
            DB::commit();
            $request->session()->forget('addressData');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('購入処理中にエラーが発生しました： ' . $e->getMessage());
            return back()->with('error', '購入処理中にエラーが発生しました。もう一度お試しください。');
        }

        return redirect('/mypage');
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
}
