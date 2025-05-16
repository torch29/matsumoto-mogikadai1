<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Profile;


class PurchaseTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    //カード決済を選択し購入するボタンを押下すると購入が完了する
    public function test_can_click_button_to_purchase_item_with_card()
    {
        $user = User::factory()
            ->has(Profile::factory())
            ->create();
        $this->actingAs($user);
        $item = Item::factory()->create();

        $response = $this->get("/purchase/{$item->id}");
        $response->assertViewIs('item.purchase.checkout');
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
                'zip_code' => $user->profile->zip_code,
                'address' => $user->profile->address,
                'building' => $user->profile->building
            ]
        ]);

        $response = $this->get('/mypage?tab=buy');
        $response->assertViewIs('user.mypage');
        $this->assertDatabaseHas('purchases', [
            'item_id' => $item->id,
            'user_id' => $user->id,
            'payment' => 'card',
            'zip_code' => $user->profile->zip_code,
            'address' => $user->profile->address,
        ]);
        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'status' => 'sold'
        ]);
    }

    //ユーザーが購入した商品は商品一覧画面（トップページ）にてsold（購入しました）と表示される
    public function test_show_sold_message_that_user_purchased_item()
    {
        $user = User::factory()
            ->has(Profile::factory())
            ->create();
        $this->actingAs($user);
        $item = Item::factory()->create();

        $response = $this->get("/purchase/{$item->id}");
        $response->assertViewIs('item.purchase.checkout');
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
                'zip_code' => $user->profile->zip_code,
                'address' => $user->profile->address,
                'building' => $user->profile->building
            ]
        ]);
        $response = $this->get('/mypage?tab=buy');
        $response = $this->get('/');
        $response->assertViewIs('index');
        $response->assertSeeInOrder([
            'class="item-purchasedItem"',
            '購入しました',
            $item->name,
        ], false);
        //$response->dump();
    }

    //購入した商品が「プロフィール/購入した商品一覧」に追加されている
    public function test_add_purchased_item_at_mypage_when_purchased()
    {
        $user = User::factory()
            ->has(Profile::factory())
            ->create();
        $this->actingAs($user);
        $item = Item::factory()->create();

        $response = $this->get("/purchase/{$item->id}");
        $response->assertViewIs('item.purchase.checkout');
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
                'zip_code' => $user->profile->zip_code,
                'address' => $user->profile->address,
                'building' => $user->profile->building
            ]
        ]);
        $response = $this->get('/mypage?tab=buy');
        $response->assertViewIs('user.mypage');
        $response->assertSeeInOrder([
            'class="item-purchasedItem"',
            '購入しました',
            $item->name,
        ], false);
        //$response->dump();
    }

    // 購入画面での支払い方法選択で、変更が即時反映される
    public function test_reflect_immediately_change_to_select_payment()
    {
        $user = User::factory()
            ->has(Profile::factory())
            ->create();
        $this->actingAs($user);
        $item = Item::factory()->create();

        $response = $this->get("/purchase/{$item->id}");
        /*
        書きかけ
        $this->visit("purchase/{item->id}")
            ->select($key, 'payment');
            */
    }

}
