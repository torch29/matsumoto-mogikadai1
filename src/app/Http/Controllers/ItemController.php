<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Category;

class ItemController extends Controller
{
    public function index()
    {
        $items = Item::with('users')->get();

        return view('index', compact('items'));
    }

    public function sell()
    {
        $conditions = Item::getConditionLabels();
        $categories = Category::all();

        return view('sell', compact('conditions', 'categories'));
    }

    public function store(Request $request)
    {
        $items = $request->only(['user_id', 'name', 'brand_name', 'price', 'explain', 'condition', 'category_ids']);
        $items['img_path'] = '';
        $item = Item::create($items);

        //画像のファイル名を、itemsのid.拡張子に変更してから更新
        $file = $request->file('img_path');
        $fileName = $item->id . '.' .  $file->getClientOriginalExtension();
        $file->storeAs('public/img/item', $fileName);

        $item->update([
            'img_path' => 'storage/img/item/' . $fileName
        ]);

        // カテゴリー登録
        $item->categories()->attach($request->input('category_ids'));


        /*
        if ($request->hasFile('img_path')) {
            $file = $request->file('img_path');
            $fileName = $item->id . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/img/', $fileName);

            $item->img_path = 'storage/img/' . $fileName;
            $item->save();
        }
            */

        return redirect('/mypage');
    }

    public function detail($id)
    {
        $item = Item::find($id);
        $categories = Item::with('categories')->get();

        return view('item', compact('item', 'categories'));
    }
}
