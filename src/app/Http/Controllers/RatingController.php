<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rating;
use App\Models\Purchase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RatingController extends Controller
{
    public function buyerRating(Request $request)
    {
        //もしログイン中ユーザーがすでにこの取引を評価しているなら、評価の再送信は弾く
        if (Rating::where('purchase_id', $request->purchaseId)
            ->where('reviewer_id', auth()->id())
            ->exists()
        ) {
            return back()->withErrors(['alert' => 'この取引はすでに評価済みです。']);
        }

        $purchase = Purchase::findOrFail($request->input('purchaseId'));

        try {
            DB::transaction(function () use ($request, $purchase) {
                Rating::create([
                    'purchase_id' => $request->input('purchaseId'),
                    'reviewer_id' => auth()->id(),
                    'reviewee_id' => $request->input('revieweeId'),
                    'score' => $request->input('score'),
                ]);

                $purchase->update([
                    'status' => 'buyer_rated',
                ]);
            });
        } catch (\Exception $e) {
            Log::error($e);
            return back()->withErrors(['alert' => '取引完了処理に失敗しました。すでに評価を送信している可能性があります']);
        }

        return redirect('/');
    }

    public function sellerRating(Request $request)
    {
        //もしログイン中ユーザーがすでにこの取引を評価しているなら、評価の再送信は弾く
        if (Rating::where('purchase_id', $request->purchaseId)
            ->where('reviewer_id', auth()->id())
            ->exists()
        ) {
            return back()->withErrors(['alert' => 'この取引はすでに評価済みです。']);
        }

        $purchase = Purchase::findOrFail($request->input('purchaseId'));

        //出品者は購入者の評価後（buyer_rated）のみ評価可能になる
        if ($purchase->status === 'trading') {
            return back()->withErrors(['alert' => '購入者が評価を送信するまでお待ちください。']);
        }

        try {
            DB::transaction(function () use ($request, $purchase) {
                Rating::create([
                    'purchase_id' => $request->input('purchaseId'),
                    'reviewer_id' => auth()->id(),
                    'reviewee_id' => $request->input('revieweeId'),
                    'score' => $request->input('score'),
                ]);

                $purchase->update([
                    'status' => 'completed',
                ]);
            });
        } catch (\Exception $e) {
            Log::error($e);
            return back()->withErrors(['message' => '取引完了処理に失敗しました。もう一度お試しください。']);
        }

        return redirect('/');
    }
}
