<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Profile;
use App\Models\Item;
use App\Models\Purchase;

class ChatViewTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    /* 「取引中」の商品を作るため、ユーザーが出品した商品が売れた状態にする設定 */
    private function createSoldItemWithUsers()
    {
        $seller = User::factory()->has(Profile::factory())->create();
        $buyer = User::factory()->has(Profile::factory())->create();
        $item = Item::factory()->create([
            'user_id' => $seller->id,
            'status' => 'sold',
        ]);
        $purchase = Purchase::factory()->create([
            'user_id' => $buyer->id,
            'item_id' => $item->id,
        ]);

        return [
            'seller' => $seller,
            'buyer' => $buyer,
            'item' => $item,
            'purchase' => $purchase,
        ];
    }

    /* 出品した商品が売れたユーザーが取引チャット画面にアクセスすると、出品者用取引チャット画面を表示する */
    public function test_display_trading_chat_for_sell_user()
    {
        //ユーザーが商品を出品し、その商品が売れた状態にする
        $seller = User::factory()->has(Profile::factory())->create();
        $buyer = User::factory()->has(Profile::factory())->create();
        $item = Item::factory()->create([
            'user_id' => $seller->id,
            'status' => 'sold',
        ]);
        $soldItem = Purchase::factory()->create([
            'user_id' => $buyer->id,
            'item_id' => $item->id,
        ]);

        //取引チャット画面にアクセスし、出品者用のビューが返ってきていることを確認
        $this->actingAs($seller);
        $response = $this->get("/mypage/chat/{$item->id}");
        $response->assertViewIs('item.trading.chat_sell_user');
        $response->assertSeeInOrder([
            $buyer->name . 'さんとの取引画面',
            $item->name,
            number_format($item->price),
        ]);
        $response->assertViewHas('tradingItem', function ($record) use ($item, $buyer) {
            return $record->purchasedUser->name === $buyer->name
                && $record->purchasedItem->name === $item->name
                && $record->purchasedItem->price === $item->price;
        });
    }

    /* ユーザーが商品を購入してから該当の商品の取引チャット画面へアクセスすると、購入者用の取引チャットビューが返ってくる */
    public function test_display_trading_chat_for_purchase_user()
    {
        //商品を購入する
        $buyer = User::factory()
            ->has(Profile::factory())
            ->create();
        //商品の出品者としてsellerを作成
        $seller = User::factory()
            ->has(Profile::factory())
            ->create();
        $item = Item::factory()->create([
            'user_id' => $seller->id, //出品者
            'status' => 'sold',
        ]);
        $purchasedItem = Purchase::factory()->create([
            'user_id' => $buyer->id, //購入者
            'item_id' => $item->id,
        ]);

        //取引チャット画面にアクセスし、購入者用のビューが返ってきていることを確認
        $this->actingAs($buyer);
        $response = $this->get("/mypage/chat/{$item->id}");
        $response->assertViewIs('item.trading.chat_purchase_user');
        $response->assertSeeInOrder([
            $seller->name . 'さんとの取引画面',
            $item->name,
            number_format($item->price),
        ]);
        $response->assertViewHas('tradingItem', function ($record) use ($item, $seller) {
            return $record->purchasedItem->users->name === $seller->name
                && $record->purchasedItem->name === $item->name
                && $record->purchasedItem->price === $item->price;
        });
    }

    /* 取引チャット画面では、他の取引中の商品が表示されている */
    public function test_display_trading_item_list_in_chat()
    {
        $seller = User::factory()->has(Profile::factory())->create();
        $buyer = User::factory()->has(Profile::factory())->create();
        //出品商品を3つ作成
        $items = Item::factory()->count(3)->create([
            'user_id' => $seller->id,
            'status' => 'sold',
        ]);
        //３つとも購入され、取引中に
        foreach ($items as $item) {
            Purchase::factory()->create([
                'user_id' => $buyer->id,
                'item_id' => $item->id,
            ]);
        }

        //取引チャット画面にアクセスし、商品名が3つとも表示されていることを確認
        $this->actingAs($seller);
        $response = $this->get("/mypage/chat/{$items->first()->id}");
        foreach ($items as $item) {
            $response->assertSee($item->name);
        }
    }
}
