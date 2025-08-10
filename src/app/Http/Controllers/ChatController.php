<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Purchase;

class ChatController extends Controller
{
    public function index($id)
    {
        //purchaseユーザーとitems.userで条件わけて、$viewの表示切り替える $viewを渡す
        $tradingItem = Purchase::with('purchasedUser', 'purchasedItem.users', 'chats', 'ratings')->where('item_id', $id)->first();

        $tradingItemList = auth()->user()->purchases()->with('purchasedItem')->get();

        return view('item.trading.chat_purchase_user', compact('tradingItem', 'tradingItemList'));
    }
}
