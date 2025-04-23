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

        return view('purchase', compact('item', 'payments', 'profile'));
    }

    public function decidePurchase(Request $request)
    {
        try {
            DB::beginTransaction();
            $purchaseData = $request->only(['item_id', 'user_id', 'payment', 'zip_code', 'address', 'building']);
            Purchase::create($purchaseData);

            $item = Item::find($request->item_id);
            $item->update(['status' => "sold"]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('購入処理中にエラーが発生しました： ' . $e->getMessage());
            return back()->with('error', '購入処理中にエラーが発生しました。もう一度お試しください。');
        }

        return redirect('/mypage');
    }


    public function changeSendAddress($id)
    {
        $user = Auth::user();
        $item_id = $id;

        return view('item.change_address');
    }

    public function saveSendAddress(Request $request)
    {
        $user = Auth::user();
        $address = $request->only(['zip_code', 'address', 'building']);
        Purchase::find($request->item_id)->create($address);

        return redirect('/mypage/profile');
    }
}
