<?php

namespace App\Http\Controllers;

use App\Mail\NotifyEmail;
use Illuminate\Http\Request;
use App\Models\Rating;
use App\Models\Purchase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class RatingController extends Controller
{
    public function buyerRating(Request $request)
    {
        //もしログイン中ユーザーがすでにこの取引を評価しているなら、評価の再送信は弾く
        if (Rating::where('purchase_id', $request->purchase_id)
            ->where('reviewer_id', auth()->id())
            ->exists()
        ) {
            return back()->withErrors(['alert' => 'この取引はすでに評価済みです。']);
        }

        $purchase = Purchase::findOrFail($request->input('purchase_id'));

        try {
            DB::transaction(function () use ($request, $purchase) {
                Rating::create([
                    'purchase_id' => $request->input('purchase_id'),
                    'reviewer_id' => auth()->id(),
                    'reviewee_id' => $request->input('reviewee_id'),
                    'score' => $request->input('score'),
                ]);

                $purchase->update([
                    'status' => 'buyer_rated',
                ]);
            });

            $sellerEmail = $purchase->purchasedItem->users->email;
            Mail::to($sellerEmail)
                ->send(new NotifyEmail($purchase));
        } catch (\Exception $e) {
            Log::error($e);
            return back()->withErrors(['alert' => '取引完了処理に失敗しました。']);
        }

        return redirect('/');
    }

    public function sellerRating(Request $request)
    {
        //もしログイン中ユーザーがすでにこの取引を評価しているなら、評価の再送信は弾く
        if (Rating::where('purchase_id', $request->purchase_id)
            ->where('reviewer_id', auth()->id())
            ->exists()
        ) {
            return back()->withErrors(['alert' => 'この取引はすでに評価済みです。']);
        }

        $purchase = Purchase::findOrFail($request->input('purchase_id'));

        //出品者は購入者の評価後（buyer_rated）のみ評価可能になる
        if ($purchase->status === 'trading') {
            return back()->withErrors(['alert' => '購入者が評価を送信するまでお待ちください。']);
        }

        try {
            DB::transaction(function () use ($request, $purchase) {
                Rating::create([
                    'purchase_id' => $request->input('purchase_id'),
                    'reviewer_id' => auth()->id(),
                    'reviewee_id' => $request->input('reviewee_id'),
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
