<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;

class PurchaseController extends Controller
{
    public function index($id)
    {
        $item = Item::find($id);

        return view('purchase', compact('item'));
    }
}
