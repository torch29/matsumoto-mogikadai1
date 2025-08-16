<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;
use App\Models\Profile;
use App\Models\Purchase;
use App\Models\Item;

class RatingFlowVisualTest extends DuskTestCase
{
    /**
     * A basic browser test example.
     *
     * @return void
     */
    use DatabaseMigrations;

    /* 取引チャット画面から[取引完了]ボタンをクリックし、
    　 評価送信→完了までの動線と見た目の流れを確認するサンプルテスト */
    public function testModalRatingAndRedirect()
    {
        $this->browse(function (Browser $browser) {
            //取引中の商品を作成するための設定
            $seller = User::factory()->has(Profile::factory())->create();
            $buyer = User::factory()->has(Profile::factory())->create();
            $item = Item::factory()->create([
                'user_id' => $seller->id,
                'status'  => 'sold',
            ]);
            $purchase = Purchase::factory()->create([
                'user_id' => $buyer->id,
                'item_id' => $item->id,
                'status' => 'trading',
            ]);
            $itemId = $item->id;

            //取引チャット画面にアクセスし、モーダルウィンドウから評価を送信
            $browser->loginAs($buyer)
                ->visit("/mypage/chat/{$itemId}")
                ->waitFor('.modal__button--open')
                ->click('.modal__button--open')
                ->waitFor('#mypopover')
                ->assertVisible('#mypopover')
                ->waitFor('label[for="star4"]')
                ->click('label[for="star4"]') //4つ目の星を選択
                ->click('.modal__button--submit') //送信ボタン
                ->waitForLocation('/') //トップページへリダイレクト
                ->assertPathIs('/')
                ->assertSee('マイリスト');
        });
    }
}
