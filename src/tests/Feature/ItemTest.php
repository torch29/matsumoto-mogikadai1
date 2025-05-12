<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;

class ItemTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    public function test_get_all_item_list()
    {
        $items = Item::factory()->count(5)->create();

        $this->assertGuest();
        $response = $this->get('/');
        $response->assertViewIs('index');
        $response->assertViewHas('items', function ($viewItems) use ($items) {
            return $viewItems->count() === 5 && $viewItems->pluck('id')->sort()->values()->all() === $items->pluck('id')->sort()->values()->all();
        });
    }

    public function test_purchased_items_display_sold_label()
    {
        $items = Item::factory()->count(5)->create();
        $items->shuffle()->take(2)->each(function ($item) {
            $item->update(['status' => 'sold']);
        });

        $response = $this->get('/');
        $response->assertSeeText('sold');
    }

    public function test_not_display_user_sell_item()
    {
        $items = Item::factory()->count(5)->create();
        $user = User::factory()->create();
        $this->actingAs($user);

        $userSoldItems = $items->shuffle()->take(2);
        $userSoldItems->each(function ($item) use ($user) {
            $item->update(['user_id' => $user->id]);
        });

        $response = $this->get('/');
        foreach ($userSoldItems as $item) {
            $response->assertDontSeeText($item->name);
        }
    }

    public function test_show_users_favorite_items_at_my_list()
    {
        $items = Item::factory()->count(5)->create();
        $user = User::factory()->create();
        $this->actingAs($user);

        $favoriteItems = $items->take(2);
        $user->favoriteItems()->syncWithoutDetaching($favoriteItems->pluck('id'));

        $response = $this->get('/');
        $expected = ['id="myList"'];
        foreach ($favoriteItems as $item) {
            $expected[] = e($item->name);
        }
        $response->assertSeeInOrder($expected, false);
    }

    public function test_show_sold_items_at_my_list()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $items = Item::factory()->count(5)->create();

        $favoriteItems = $items->take(2);
        $user->favoriteItems()->syncWithoutDetaching($favoriteItems->pluck('id'));
        foreach ($favoriteItems as $item) {
            $item->update([
                'status' => 'sold'
            ]);
        }

        $response = $this->get('/');
        $expected = ['id="myList"'];
        foreach ($favoriteItems as $item) {
            $expected[] = e($item->name);
        }
        $response->assertSeeText('sold');
        $response->assertSeeInOrder($expected, false);
    }

    //10の商品購入機能はここを修正すれば行けそう
    public function test_show_users_purchased_items()
    {
        $sellUser = User::factory()->create();
        $buyUser = User::factory()->create();
        $this->actingAs($buyUser);

        $items = Item::factory()->count(5)->create();

        $purchasedItems = $items->take(2);
        //ユーザーが購入
        foreach ($purchasedItems as $item) {
            $item->update([
                'status' => 'sold'
            ]);
            Purchase::create([
                'user_id' => $buyUser->id,
                'item_id' => $item->id,
                'payment' => 'card',
                'zip_code' => '0000000',
                'address' => '北海道札幌市',
            ]);
        }

        $response = $this->get('/mypage');
        $expected = ['id="purchasedItems"'];
        foreach ($purchasedItems as $item) {
            $expected[] = e($item->name);
        }
        $response->assertSeeText('購入しました');
        $response->assertSeeInOrder($expected, false);
    }
}
