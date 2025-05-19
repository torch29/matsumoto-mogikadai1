<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Profile;
use Mockery;

class PurchaseKonbiniTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use RefreshDatabase;

    //コンビニ決済（"konbini"）を選択し、購入するボタンを押下すると購入が完了する
    public function test_can_purchase_item_with_konbini()
    {
        $this->withoutExceptionHandling();

        $mock = Mockery::mock(\App\Services\StripeService::class);
        $this->app->instance(\App\Services\StripeService::class, $mock);
        $mock->shouldReceive('createCheckoutSession')->once()->andReturn((object)[
            'url' => 'https://fake-stripe-checkout-url.com',
        ]);

        $user = User::factory()
            ->has(Profile::factory())
            ->create();
        $item = Item::factory()->create();
        $this->actingAs($user);

        //購入画面にアクセスし、コンビニ決済を選んで購入
        $response = $this->get("/purchase/{$item->id}");
        $response->assertViewIs('item.purchase.checkout');
        $response = $this->post(route('purchase.checkout', ['itemId' => $item->id]), [
            'item_id' => $item->id,
            'user_id' => $user->id,
            'payment' => 'konbini',
            'zip_code' => $user->profile->zip_code,
            'address' => $user->profile->address,
            'building' => $user->profile->building
        ]);

        $response = $this->get('/mypage?tab=buy');
        $response->assertViewIs('user.mypage');

        //データベースに購入データが保存＆カラムの更新がされているか確認
        $this->assertDatabaseHas('purchases', [
            'item_id' => $item->id,
            'user_id' => $user->id,
            'payment' => 'konbini',
            'zip_code' => $user->profile->zip_code,
            'address' => $user->profile->address,
        ]);
        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'status' => 'pending'
        ]);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
