<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Profile;

class PurchaseAddressChangeTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use RefreshDatabase;

    public function test_reflects_changed_shipping_address()
    {
        $user = User::factory()
            ->has(Profile::factory())
            ->create();
        $this->actingAs($user);
        $item = Item::factory()->create();

        //購入画面→住所変更ページを開く
        $response = $this->get("/purchase/{$item->id}");
        $response = $this->get("/purchase/address/{$item->id}");
        $response->assertViewIs('item.purchase.change_address');

        //住所を変更後、購入画面に戻って住所変更が反映されていることを確認
        $this->post("/purchase/address/{$item->id}", [
            'zip_code' => '000-1234',
            'address' => '北海道札幌市123',
            'building' => '住所変更マンション22'
        ]);
        $response = $this->get("/purchase/{$item->id}");
        $response->assertSeeInOrder([
            '000-1234',
            '北海道札幌市123',
            '住所変更マンション22',
        ], false);
        //$response->dump();
    }

    public function test_save_changed_address_with_purchased_item()
    {
        $user = User::factory()
            ->has(Profile::factory())
            ->create();
        $this->actingAs($user);
        $item = Item::factory()->create();

        //最初に購入画面を表示時は、プロフィールに登録された住所が表示されるか確認
        $response = $this->get("/purchase/{$item->id}");
        $response->assertSeeInOrder([
            $user->profile->zip_code,
            $user->profile->address,
        ]);

        //住所変更ページにアクセスし、住所を変更する
        $response = $this->get("/purchase/address/{$item->id}");
        $response->assertViewIs('item.purchase.change_address');
        $this->post("/purchase/address/{$item->id}", [
            'zip_code' => '000-1234',
            'address' => '北海道札幌市123',
            'building' => '住所変更マンション22'
        ]);

        //購入画面にて、住所変更が反映されている
        $response = $this->get("/purchase/{$item->id}");
        $response->assertSeeInOrder([
            '000-1234',
            '北海道札幌市123',
            '住所変更マンション22',
        ], false);

        //購入関連の処理
        $response = $this->post(route('purchase.checkout', ['itemId' => $item->id]), [
            'item_id' => $item->id,
            'user_id' => $user->id,
            'payment' => 'card',
            'zip_code' => '000-1234',
            'address' => '北海道札幌市123',
            'building' => '住所変更マンション22'
        ]);
        $this->withSession([
            'purchased_item_id' => $item->id,
            'purchased_payment' => 'card',
            'purchased_address' => [
                'zip_code' => '000-1234',
                'address' => '北海道札幌市123',
                'building' => '住所変更マンション22'
            ]
        ]);
        $response = $this->get('/mypage?tab=buy');
        $response->assertViewIs('user.mypage');

        //データベースに保存＆更新が行われたか確認
        $this->assertDatabaseHas('purchases', [
            'item_id' => $item->id,
            'user_id' => $user->id,
            'payment' => 'card',
            'zip_code' => '000-1234',
            'address' => '北海道札幌市123',
            'building' => '住所変更マンション22'
        ]);
        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'status' => 'sold'
        ]);
        //$response->dump();
    }
}
