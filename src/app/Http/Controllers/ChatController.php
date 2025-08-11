<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chat;
use App\Models\Purchase;
use App\Http\Requests\ChatRequest;

class ChatController extends Controller
{
    public function index($id)
    {
        //purchaseユーザーとitems.userで条件わけて、$viewの表示切り替える $viewを渡す
        $tradingItem = Purchase::with('purchasedUser', 'purchasedItem.users.profile', 'chats', 'ratings')->where('item_id', $id)->first();

        $tradingItemList = auth()->user()->purchases()->with('purchasedItem')->get();

        $chats = Chat::with('tradingPurchaseItem', 'sendUser')->where('purchase_id', $tradingItem->id)->get();

        return view('item.trading.chat_purchase_user', compact('tradingItem', 'tradingItemList', 'chats'));
    }

    public function send(ChatRequest $request)
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
                'img_path' => 'storage/img/chat/' . $fileName
            ]);
        }

        return back();
    }

    public function update(ChatRequest $request)
    {
        $chat = Chat::findOrFail($request->id);

        if ($chat->sender_id !== auth()->id()) {
            abort(403, '自分以外のメッセージは編集できません。');
        }
        $chat->update($request->only(['$message']));

        $transitionId = $request->input('transitionId');

        return redirect('mypage/chat/' . $transitionId);
    }

    public function destroy(Request $request)
    {
        $chat = Chat::findOrFail($request->id);

        if ($chat->sender_id !== auth()->id()) {
            abort(403, '自分以外のメッセージは編集できません。');
        }
        $chat->delete();

        $transitionId = $request->input('transitionId');

        return redirect('mypage/chat/' . $transitionId);
    }
}
