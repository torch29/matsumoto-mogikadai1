<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;

class ItemIndexTest extends TestCase
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

    public function test_display_users_favorite_items_at_my_list()
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

        $response->assertViewIs('index');
        $response->assertViewHas('myLists');
        $response->assertSeeInOrder($expected, false);
    }

    public function test_display_sold_items_label_at_my_list()
    {
        $user = User::factory()->create();
        $loginUser = User::factory()->create();
        $this->actingAs($loginUser);
        $items = Item::factory()->count(5)->create(['user_id' => $user->id]);

        $favoriteItems = $items->take(2);
        $loginUser->favoriteItems()->syncWithoutDetaching($favoriteItems->pluck('id'));
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
        $response->assertViewHas('myLists');
        $response->assertSeeText('sold');
        $response->assertSeeInOrder($expected, false);
    }

    public function test_not_display_user_sell_item_at_my_list()
    {
        $items = Item::factory()->count(5)->create();
        $user = User::factory()->create();
        $this->actingAs($user);

        $userSoldItems = $items->shuffle()->take(2);
        $userSoldItems->each(function ($item) use ($user) {
            $item->update(['user_id' => $user->id]);
        });
        $favoriteItems = $userSoldItems->take(2);
        $user->favoriteItems()->syncWithoutDetaching($favoriteItems->pluck('id'));

        $response = $this->get('/');
        foreach ($favoriteItems as $favoriteItem) {
            $response->assertDontSeeText($favoriteItem->name);
        }
    }

    public function test_not_display_any_item_guest_user_at_my_list()
    {
        $items = Item::factory()->count(5)->create();
        $user = User::factory()->create();

        $favoriteItems = $items->take(2);
        $user->favoriteItems()->syncWithoutDetaching($favoriteItems->pluck('id'));

        $response = $this->get('/');
        $this->assertGuest();

        $response->assertSee('いいねをした商品がこちらに表示されます');
        $response->assertViewIs('index');
        $response->assertViewHas('myLists', function ($myLists) {
            return $myLists->isEmpty();
        });
    }

    public function test_can_partial_match_search()
    {
        $user = User::factory()->create();
        $this->assertGuest();

        Item::factory()->create(['name' => '壁掛け時計']);
        Item::factory()->create(['name' => '腕時計']);
        Item::factory()->create(['name' => 'ショルダーバッグ']);

        $response = $this->get('/?search=時計');
        $response->assertViewIs('index');

        $response->assertSee('壁掛け時計');
        $response->assertSee('腕時計');
        $response->assertDontSee('ショルダーバッグ');
        $targetItems = Item::NameSearch('時計')->get();
        $this->assertCount(2, $targetItems);
    }

    public function test_can_partial_match_search_sustained_at_my_list()
    {
        $user = User::factory()->create();
        $item1 = Item::factory()->create(['name' => '壁掛け時計']);
        $item2 = Item::factory()->create(['name' => '腕時計']);
        $item3 = Item::factory()->create(['name' => 'ショルダーバッグ']);
        $item4 = Item::factory()->create(['name' => 'コーヒーミル']);
        $item5 = Item::factory()->create(['name' => 'コーヒーカップ']);

        $this->actingAs($user);
        $user->favoriteItems()->syncWithoutDetaching([
            $item1->id, //時計を含む
            $item4->id, //時計を含まない
        ]);

        $response = $this->get('/?search=時計');
        $response->assertViewIs('index');

        $response->assertSee('壁掛け時計');
        $response->assertDontSee('コーヒーミル'); //時計を含まない
        $targetItems = Item::NameSearch('時計')->get();

        $response->assertSeeInOrder([
            'id="myList"',
            e($item1->name),
        ], false);

        $response->assertViewHas('myLists', function ($myLists) {
            return $myLists->count() === 1 &&
                $myLists->pluck('name')->contains('壁掛け時計') &&
                !$myLists->pluck('name')->contains('コーヒーミル') &&
                !$myLists->pluck('name')->contains('腕時計');
        });
    }



    /*

    //10の商品購入機能はここを修正すれば行けそう
    public function test_display_users_purchased_items()
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

    */
}
