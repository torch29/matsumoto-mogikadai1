<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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

    public function test_reflect_that_changed_shipping_address()
    {
        $user = User::factory()
            ->has(Profile::factory())
            ->create();
        $this->actingAs($user);
        $item = Item::factory()->create();

        $response = $this->get("/purchase/{$item->id}");
        $response = $this->get("/purchase/address/{$item->id}");
        $response->assertViewIs('item.purchase.change_address');
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

        //最初に購入画面表示時は、プロフィールに登録された住所が表示されている
        $response = $this->get("/purchase/{$item->id}");
        $response->assertSeeInOrder([
            $user->profile->zip_code,
            $user->profile->address,
        ]);

        $response = $this->get("/purchase/address/{$item->id}");
        $response->assertViewIs('item.purchase.change_address');
        $this->post("/purchase/address/{$item->id}", [
            'zip_code' => '000-1234',
            'address' => '北海道札幌市123',
            'building' => '住所変更マンション22'
        ]);

        //住所変更が反映されている
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
            'zip_code' => $user->profile->zip_code,
            'address' => $user->profile->address,
            'building' => $user->profile->building
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
