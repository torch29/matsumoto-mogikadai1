<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index()
    {
        //purchaseユーザーとitems.userで条件わけて、$viewの表示切り替える $viewを渡す

        return view('item.trading.chat_purchase_user');
    }
}
