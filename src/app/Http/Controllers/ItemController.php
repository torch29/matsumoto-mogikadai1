<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Category;
use App\Models\Comment;
use App\Models\User;
use App\Models\Purchase;
use App\Http\Requests\CommentRequest;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $word = $request->search;

        $items = Item::with('users', 'purchases')
            ->NameSearch($word)
            ->get();

        $myLists = Auth::user()
            ? Auth::user()->favoriteItems()
            ->when($word, function ($query, $word) {
                $query->where('name', 'like', "%{$word}%");
            })
            ->get()
            : collect();

        $purchasedItemIds = Purchase::where('user_id', Auth::id())->pluck('item_id')->toArray();

        // dump($purchasedItemIds);

        return view('index', compact('items', 'myLists', 'word', 'purchasedItemIds'));
    }

    public function sell()
    {
        $conditions = Item::getConditionLabels();
        $categories = Category::all();

        return view('item.sell', compact('conditions', 'categories'));
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

        return redirect('/mypage?tab=sell');
    }

    public function detail($id)
    {
        $item = Item::with([
            'comments' => function ($query) {
                $query->orderBy('created_at', 'desc');
            },
            'comments.user.profile'
        ])->find($id);
        $categories = Item::with('categories')->get();

        $purchasedItemIds = Purchase::where('user_id', Auth::id())->pluck('item_id')->toArray();

        return view('item.detail', compact('item', 'categories', 'purchasedItemIds'));
    }

    public function postComment(CommentRequest $request)
    {
        $comment = [
            'item_id' => $request->input('item_id'),
            'user_id' => Auth::id(),
            'comment' => $request->input('comment'),
        ];
        Comment::create($comment);

        return back();
    }
}
