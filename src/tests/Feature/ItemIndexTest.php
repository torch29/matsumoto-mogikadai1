<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
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

    //商品一覧にて全商品を取得できる
    public function test_display_all_item_in_index()
    {
        //商品名を指定して3件作成
        $item1 = Item::factory()->create([
            'name' => 'テスト時計',
            'img_path' => 'watch.jpg',
        ]);
        $item2 = Item::factory()->create([
            'name' => 'テスト鞄',
            'img_path' => 'images/item/bag.jpg'
        ]);
        $item3 = Item::factory()->create([
            'name' => 'テスト教科書',
            'img_path' => 'book.jpg'
        ]);
        $this->assertGuest();

        //トップページにアクセスし、作成された商品名と同じものが表示されていることを確認
        $response = $this->get('/');
        $response->assertViewIs('index');
        $response->assertSee([
            'テスト時計',
            'テスト鞄',
            'テスト教科書'
        ]);
        $response->assertViewHas('items', function ($showItems) use ($item1, $item2, $item3) {
            $expectedIds = collect([$item1->id, $item2->id, $item3->id])->sort()->values()->all();
            $viewIds = $showItems->pluck('id')->sort()->values()->all();
            return $showItems->count() === 3 && $viewIds === $expectedIds;
        });
        //$response->dump();
    }

    //商品一覧にて購入済み商品は「sold」と表示される
    public function test_display_sold_label_for_purchased_items_in_index()
    {
        //作成された5件のうち2件のstatusをsoldにする
        $items = Item::factory()->count(5)->create();
        $soldItems = $items->take(2);
        foreach ($soldItems as $soldItem) {
            $soldItem->update(['status' => 'sold']);
        }

        //トップページにアクセスし、sold表示があることを確認
        $response = $this->get('/');
        $response->assertSeeText('sold');
        foreach ($soldItems as $soldItem) {
            $response->assertSeeInOrder([
                'class="item-sold"',
                $soldItem->name,
            ], false);
        }
    }

    //商品一覧にて、自分が出品した商品は表示されない
    public function test_not_display_users_own_items_in_index()
    {
        $items = Item::factory()->count(5)->create();
        $user = User::factory()->create();
        $this->actingAs($user);

        //商品のうち2つをユーザーが出品した状態にする
        $userSoldItems = $items->shuffle()->take(2);
        $userSoldItems->each(function ($item) use ($user) {
            $item->update(['user_id' => $user->id]);
        });

        //トップページにアクセスし、ユーザーが出品した商品が表示されていないことを確認
        $response = $this->get('/');
        foreach ($userSoldItems as $item) {
            $response->assertDontSeeText($item->name);
        }
    }

    //マイリストにて、いいねした商品のみ表示
    public function test_display_users_favorite_items_in_my_list()
    {
        //商品を5つとユーザーを作成
        $items = Item::factory()->count(5)->create();
        $user = User::factory()->create();
        $this->actingAs($user);

        //商品のうち2つにいいねをつけた状態にする
        $favoriteItems = $items->take(2);
        $user->favoriteItems()->syncWithoutDetaching($favoriteItems->pluck('id'));

        //トップページにアクセスし、タブ名myList→商品名の順に表示（マイリストタブ内に表示される）されていることを確認
        $response = $this->get('/');
        $expected = ['id="myList"'];
        foreach ($favoriteItems as $favoriteItem) {
            $expected[] = e($favoriteItem->name);
        }
        $response->assertViewIs('index');
        $response->assertViewHas('myLists');
        $response->assertSeeInOrder($expected, false);
    }

    //マイリストにて、売り切れ商品はsoldと表示される
    public function test_display_sold_label_for_item_in_my_list()
    {
        //商品を5つと、ユーザーを作成する
        $user = User::factory()->create();
        $loginUser = User::factory()->create();
        $this->actingAs($loginUser);
        $items = Item::factory()->count(5)->create(['user_id' => $user->id]);

        //商品のうち2つにいいねをつけた状態にし、尚且つ売り切れた状態にする
        $favoriteItems = $items->take(2);
        $loginUser->favoriteItems()->syncWithoutDetaching($favoriteItems->pluck('id'));
        foreach ($favoriteItems as $favoriteItem) {
            $favoriteItem->update([
                'status' => 'sold'
            ]);
        }

        $response = $this->get('/');
        $response->assertViewHas('myLists');
        //マイリストタブ内でsoldラベル->商品名の順で表示されている
        foreach ($favoriteItems as $favoriteItem) {
            $response->assertSeeInOrder([
                'id="myList"',
                'item-sold',
                e($favoriteItem->name),
            ], false);
        }
        //$response->dump();
    }

    //マイリストにて、自分が出品した商品は表示されない
    public function test_not_display_users_own_item_in_my_list()
    {
        //商品を5つとユーザーを作成
        $items = Item::factory()->count(5)->create();
        $user = User::factory()->create();
        $this->actingAs($user);

        //商品のうち2つをユーザーが出品した状態にする
        $userSoldItems = $items->shuffle()->take(2);
        $userSoldItems->each(function ($item) use ($user) {
            $item->update(['user_id' => $user->id]);
        });
        //ユーザー出品した商品にいいねをつけた状態に
        $favoriteItems = $userSoldItems->take(2);
        $user->favoriteItems()->syncWithoutDetaching($favoriteItems->pluck('id'));

        $response = $this->get('/');
        //マイリストタブ内に自分が出品した商品名がない
        foreach ($favoriteItems as $favoriteItem) {
            $response->assertDontSeeText($favoriteItem->name);
        }
    }

    //マイリストには未認証ユーザーの場合何も表示されない
    public function test_not_display_any_item_to_guest_in_my_list()
    {
        //商品を5つ作成
        $items = Item::factory()->count(5)->create();
        $user = User::factory()->create();

        //5つのうち2つにいいねをつけた状態に
        $favoriteItems = $items->take(2);
        $user->favoriteItems()->syncWithoutDetaching($favoriteItems->pluck('id'));

        //ゲストユーザーでトップページにアクセスする
        $response = $this->get('/');
        $this->assertGuest();

        //マイリストに商品が何も表示されていない（「いいねをした商品がこちらに表示されます」というテキストの表示のみである）ことを確認
        $response->assertSee('いいねをした商品がこちらに表示されます');
        $response->assertViewIs('index');
        $response->assertViewHas('myLists', function ($myLists) {
            return $myLists->isEmpty();
        });
    }

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
            $item1->id, //時計を含む商品（壁掛け時計）
            $item4->id, //時計を含まない商品（コーヒーミル）
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
