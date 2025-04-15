<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;

class ItemController extends Controller
{
    public function index()
    {
        return view('index');
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

    public function showItem()
    {
        return view('item');
    }
}
