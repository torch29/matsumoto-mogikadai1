<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;

class ItemController extends Controller
{
    public function index()
    {
        $items = Item::with('users')->get();

        return view('index', compact('items'));
    }

    public function sell()
    {
        return view('sell');
    }

    public function store(Request $request)
    {
        $items = $request->only(['id', 'user_id', 'name', 'brand_name', 'price', 'explain', 'condition', 'img_path']);
        Item::create($items);
    }

    public function detail()
    {

        return view('item');
    }
}
