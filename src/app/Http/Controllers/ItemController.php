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
        $conditions = [
            1 => '良好',
            2 => '目立った傷や汚れなし',
            3 => 'やや傷や汚れあり',
            4 => '状態が悪い'
        ];
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
        $file->storeAs('public/img/', $fileName);

        $item->update([
            'img_path' => 'storage/img/' . $fileName
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

    public function detail()
    {

        return view('item');
    }
}
