<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;

class ItemSearchTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use RefreshDatabase;

    //商品名で部分一致検索ができる
    public function test_can_search_items_by_partial_match()
    {
        $user = User::factory()->create();
        $this->assertGuest();

        //商品を３つ作成
        Item::factory()->create(['name' => '壁掛け時計']);
        Item::factory()->create(['name' => '腕時計']);
        Item::factory()->create(['name' => 'ショルダーバッグ']);

        //キーワード"時計"で検索
        $response = $this->get('/?search=時計');
        $response->assertViewIs('index');

        //"時計"を含む２つの商品が表示され、時計を含まない商品が表示されていないことを確認
        $response->assertSee('壁掛け時計');
        $response->assertSee('腕時計');
        $response->assertDontSee('ショルダーバッグ');
        $targetItems = Item::NameSearch('時計')->get();
        $this->assertCount(2, $targetItems);
    }

    //検索状態がマイリストでも保持されている
    public function test_can_search_condition_persists_in_my_list()
    {
        //ユーザーと、検索用にアイテムを5つ作成
        $user = User::factory()->create();
        $item1 = Item::factory()->create(['name' => '壁掛け時計']);
        $item2 = Item::factory()->create(['name' => '腕時計']);
        $item3 = Item::factory()->create(['name' => 'ショルダーバッグ']);
        $item4 = Item::factory()->create(['name' => 'コーヒーミル']);
        $item5 = Item::factory()->create(['name' => 'コーヒーカップ']);

        //商品のうち２つにいいねをつけた状態にする
        $this->actingAs($user);
        $user->favoriteItems()->syncWithoutDetaching([
            $item1->id, //時計を含む（壁掛け時計）
            $item4->id, //時計を含まない（コーヒーミル）
        ]);

        //"時計"で検索し、壁掛け時計が表示され、コーヒーミルが表示されていないことを確認
        $response = $this->get('/?search=時計');
        $response->assertViewIs('index');

        $response->assertSee('壁掛け時計');
        $response->assertDontSee('コーヒーミル'); //時計を含まない
        $targetItems = Item::NameSearch('時計')->get();

        //マイリストでも壁掛け時計が表示されていることを確認
        $response->assertSeeInOrder([
            'id="myList"',
            e($item1->name),
        ], false);

        //いいねがあり時計を含む壁掛け時計は該当する、いいねがあるが時計を含まないコーヒーミルは該当しない、時計を含むがいいねをしていない腕時計は該当しない
        $response->assertViewHas('myLists', function ($myLists) {
            return $myLists->count() === 1 &&
                $myLists->pluck('name')->contains('壁掛け時計') &&
                !$myLists->pluck('name')->contains('コーヒーミル') &&
                !$myLists->pluck('name')->contains('腕時計');
        });
    }
}
