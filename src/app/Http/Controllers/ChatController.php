<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chat;
use App\Models\Purchase;

class ChatController extends Controller
{
    public function index($id)
    {
        //purchaseユーザーとitems.userで条件わけて、$viewの表示切り替える $viewを渡す
        $tradingItem = Purchase::with('purchasedUser', 'purchasedItem.users', 'chats', 'ratings')->where('item_id', $id)->first();

        $tradingItemList = auth()->user()->purchases()->with('purchasedItem')->get();

        $chats = Chat::with('tradingPurchaseItem.purchasedUser')->where('purchase_id', $tradingItem->id)->get();

        return view('item.trading.chat_purchase_user', compact('tradingItem', 'tradingItemList', 'chats'));
    }

    public function send(Request $request)
    {
        $chats = [
            'purchase_id' => $request->input('purchase_id'),
            'sender_id' => auth()->id(),
            'message' => $request->input('message'),
            'img_path' => '',
        ];
        $chat = Chat::create($chats);

        if ($request->hasFile('img_path')) {
            $file = $request->file('img_path');
            $fileName = $chat->id . '.' .
                $file->getClientOriginalExtension();
            $file->storeAs('public/img/chat', $fileName);

            $chat->update([
                'img_path' => 'storage/img/item' . $fileName
            ]);
        }

        return back();
    }
}
