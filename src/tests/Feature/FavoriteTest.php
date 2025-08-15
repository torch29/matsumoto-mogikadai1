<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;

class FavoriteTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use RefreshDatabase;

    //いいねアイコンの押下によっていいねした商品として登録され、合計値が増加表示する
    public function test_save_favorite_items_when_user_click_icon()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $item = Item::factory()->create();

        //商品詳細画面にアクセスし、いいねが0件であることを確認
        $response = $this->get("/item/{$item->id}");
        $response->assertViewHas('item', function ($item) {
            return $item->favoriteUsers->count() === 0;
        });
        $response->assertSeeInOrder([
            'class="item__favorite-form"',
            0,
        ], false);

        //いいねを投稿すると、データベースに保存されることを確認
        $this->post("/favorite/{$item->id}", [
            'item_id' => $item->id,
            'user_id' => $user->id
        ]);
        $this->assertDatabaseHas('favorites', [
            'item_id' => $item->id,
            'user_id' => $user->id
        ]);

        //商品詳細画面にて、いいねの件数が1件に増えていることを確認
        $response = $this->get("/item/{$item->id}");
        $response->assertViewHas('item', function ($item) {
            return $item->favoriteUsers->count() === 1;
        });
        $response->assertSeeInOrder([
            'class="item__favorite-form"',
            1,
        ], false);
    }

    //いいね追加済みのアイコンは色が変化する
    public function test_change_color_of_icon_when_item_is_favorited()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $item = Item::factory()->create();

        //商品詳細画面にアクセスし、いいねする前のアイコンを確認
        $response = $this->get("/item/{$item->id}");
        $response->assertSee('fa-regular fa-star');
        $response->assertDontSee('fa-solid fa-star');

        //いいねをつける
        $this->post("/favorite/{$item->id}", [
            'item_id' => $item->id,
            'user_id' => $user->id
        ]);
        Auth::login($user->fresh());

        //いいねの件数が1件に増え、アイコンの見た目が変化していることを確認
        $response = $this->get("/item/{$item->id}");
        $response->assertViewHas('item', function ($item) {
            return $item->favoriteUsers->count() === 1;
        });
        $response->assertSee('fa-solid fa-star');
        $response->assertDontSee('fa-regular fa-star');
    }

    //いいね済みアイコンを再度押下するといいねが解除され、合計値が減少表示される
    public function test_remove_favorite_when_icon_is_clicked_again()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $item = Item::factory()->create();

        $user->favoriteItems()->syncWithoutDetaching([
            'item_id' => $item->id
        ]);

        //商品詳細画面にアクセスし、いいねされた状態のアイコンであることと、いいね件数が1件であることを確認
        $response = $this->get("/item/{$item->id}");
        $response->assertSee('fa-solid fa-star');
        $response->assertDontSee('fa-regular fa-star');
        $response->assertViewHas('item', function ($item) {
            return $item->favoriteUsers->count() === 1;
        });
        $response->assertSeeInOrder([
            'class="item__favorite-form"',
            1,
        ], false);
        $this->assertDatabaseHas('favorites', [
            'item_id' => $item->id,
            'user_id' => $user->id
        ]);

        //再度いいねをクリック（いいねを解除する）
        $this->delete("/favorite/{$item->id}", [
            'item_id' => $item->id,
            'user_id' => $user->id
        ]);
        Auth::login($user->fresh());

        //商品詳細画面にて、いいねの件数が0件に減少し、アイコンの見た目が変化している（戻っている）ことを確認
        $response = $this->get("/item/{$item->id}");
        $response->assertSee('fa-regular fa-star');
        $response->assertDontSee('fa-solid fa-star');
        $response->assertViewHas('item', function ($item) {
            return $item->favoriteUsers->count() === 0;
        });
        $response->assertSeeInOrder([
            'class="item__favorite-form"',
            0,
        ], false);
        $this->assertDatabaseMissing('favorites', [
            'item_id' => $item->id,
            'user_id' => $user->id
        ]);
    }
}
