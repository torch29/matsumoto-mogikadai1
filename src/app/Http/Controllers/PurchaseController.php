<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
}
