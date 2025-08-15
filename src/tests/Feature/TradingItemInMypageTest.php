<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Profile;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\Chat;
use Mockery;

class TradingItemInMypageTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    /* ユーザーが商品購入後、商品が「取引中」となる */
    public function test_display_trading_items_when_user_purchased_item()
    {
        $user = User::factory()
            ->has(Profile::factory())
            ->create();
        $item = Item::factory()->create();
        $this->actingAs($user);

        //カード決済で商品購入
        $response = $this->get("/purchase/{$item->id}");
        $response = $this->post(route('purchase.checkout', ['itemId' => $item->id]), [
            'item_id' => $item->id,
            'user_id' => $user->id,
            'payment' => 'card',
            'zip_code' => $user->profile->zip_code,
            'address' => $user->profile->address,
            'building' => $user->profile->building
        ]);

        //セッションをもってマイページへ戻ってくる（この段階でデータベースに登録）
        $this->withSession([
            'purchased_item_id' => $item->id,
            'purchased_payment' => 'card',
            'purchased_address' => [
                'zip_code' => $user->profile->zip_code,
                'address' => $user->profile->address,
                'building' => $user->profile->building
            ]
        ]);
        //マイページ内 取引中の商品欄に商品名が表示されていることを確認
        $response = $this->get('/mypage?tab=buy');
        $response->assertSeeInOrder([
            '取引中の商品',
            $item->name,
        ]);
        //purchasesデータベースに登録される際、statusがtradingとなっていることを確認
        $this->assertDatabaseHas('purchases', [
            'item_id' => $item->id,
            'user_id' => $user->id,
            'status' => 'trading',
        ]);
    }

    /* ユーザーが出品した商品が売れた場合、その商品が「取引中」となる */
    public function test_display_trading_items_when_item_sold()
    {
        //konbini決済のための準備
        $this->withoutExceptionHandling();
        $mock = Mockery::mock(\App\Services\StripeService::class);
        $this->app->instance(\App\Services\StripeService::class, $mock);
        $mock->shouldReceive('createCheckoutSession')->once()->andReturn((object)[
            'url' => 'https://fake-stripe-checkout-url.com',
        ]);

        //出品者としてユーザー１を作成
        $user1 = User::factory()->create();
        //購入者としてユーザー２を作成
        $user2 = User::factory()
            ->has(Profile::factory())
            ->create();

        //ユーザー1が出品した商品を用意
        $item = Item::factory()->create([
            'user_id' => $user1->id,
        ]);

        //user2がコンビニ決済で商品を購入する
        $this->actingAs($user2);
        $response = $this->get("/purchase/{$item->id}");
        $response = $this->post(route('purchase.checkout', ['itemId' => $item->id]), [
            'item_id' => $item->id,
            'user_id' => $user2->id,
            'payment' => 'konbini',
            'zip_code' => $user2->profile->zip_code,
            'address' => $user2->profile->address,
            'building' => $user2->profile->building
        ]);
        $response = $this->get('/mypage?tab=buy');

        //purchasesデータベースに登録される際、statusがtradingとなっていることを確認
        $this->assertDatabaseHas('purchases', [
            'item_id' => $item->id,
            'user_id' => $user2->id,
            'status' => 'trading',
        ]);

        //user1がマイページに訪問、取引中の商品欄に商品名が表示されていることを確認
        $this->actingAs($user1);
        $response = $this->get('/mypage?tab=buy');
        $response->assertSeeInOrder([
            '取引中の商品',
            $item->name,
        ]);
    }

    /* 取引中の商品欄には出品して売れた商品と自分が購入した商品の両方が表示される */
    public function test_display_trading_item_list_in_mypage()
    {
        $user = User::factory()
            ->has(Profile::factory())
            ->create();
        $user2 = User::factory()
            ->has(Profile::factory())
            ->create();
        //ユーザーが出品した商品を作成し、ユーザー２によって購入された状態になる
        $userSoldItem = Item::factory()->create([
            'user_id' => $user->id,
            'status' => 'sold',
        ]);
        $userSoldItem = Purchase::factory()->create([
            'user_id' => $user2->id,
            'item_id' => $userSoldItem->id,
            'payment' => 'card',
            'status' => 'trading',
        ]);
        //ユーザー１が商品を購入した状態にする
        $itemForPurchase = Item::factory()->create([
            'status' => 'sold',
        ]);
        $userPurchasedItem = Purchase::factory()->create([
            'user_id' => $user->id,
            'item_id' => $itemForPurchase->id,
        ]);

        //マイページにアクセスし、出品して売れた商品と購入した商品の両方が表示されていることを確認
        $this->actingAs($user);
        $response = $this->get('mypage');
        $response->assertSeeInOrder([
            'tradingItems',
            $userSoldItem->name,
        ]);
        $response->assertSeeInOrder([
            'tradingItems',
            $userPurchasedItem->name,
        ]);

        $response->assertViewHas('tradingItems', function ($records) use ($userSoldItem, $userPurchasedItem) {
            $names = $records->pluck('name')->all();

            return in_array($userSoldItem->purchasedItem->name, $names, true) &&
                in_array($userPurchasedItem->purchasedItem->name, $names, true);
        });
    }

    /* 取引中の商品欄は、取引チャットの新着メッセージ順に並ぶ */
    public function test_items_are_sorted_by_latest_message_in_trading_item_tab()
    {
        //ユーザーが購入した商品を２つ作成
        $user = User::factory()->create();
        $item1 = Item::factory()->create([
            'status' => 'sold'
        ]);
        $item2 = Item::factory()->create([
            'status' => 'sold'
        ]);
        $purchasedItem1 = Purchase::factory()->create([
            'user_id' => $user->id,
            'item_id' => $item1->id,
        ]);
        $purchasedItem2 = Purchase::factory()->create([
            'user_id' => $user->id,
            'item_id' => $item2->id,
        ]);

        //チャットの日付を設定
        Chat::factory()->create([
            'purchase_id' => $purchasedItem1->id,
            'sender_id' => $user->id,
            'created_at' => now()->subDay(),
        ]);
        Chat::factory()->create([
            'purchase_id' => $purchasedItem2->id,
            'sender_id' => $user->id,
            'created_at' => now(),
        ]);

        //マイページにアクセスし、古→新の順で表示されていることを確認
        $this->actingAs($user);
        $response = $this->get('/mypage?tab=buy');

        $html = $response->getContent();
        $pos2 = strpos($html, $item2->name);
        $pos1 = strpos($html, $item1->name);
        $this->assertTrue($pos2 < $pos1);
        $response->assertSeeInOrder([
            $item2->name,
            $item1->name,
        ]);
    }
}
