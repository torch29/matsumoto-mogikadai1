<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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
    public function test_save_favorite_items_that_user_click_icon()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $item = Item::factory()->create();

        $response = $this->get("/item/{$item->id}");
        $response->assertViewHas('item', function ($item) {
            return $item->favoriteUsers->count() === 0;
        });
        $response->assertSeeInOrder([
            'class="item__favorite-form"',
            0,
        ], false);
        $this->post("/favorite/{$item->id}", [
            'item_id' => $item->id,
            'user_id' => $user->id
        ]);
        $this->assertDatabaseHas('favorites', [
            'item_id' => $item->id,
            'user_id' => $user->id
        ]);
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
    public function test_change_icon_color_that_saved_user_favorite()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $item = Item::factory()->create();

        $response = $this->get("/item/{$item->id}");
        $response->assertSee('fa-regular fa-star');
        $response->assertDontSee('fa-solid fa-star');
        $this->post("/favorite/{$item->id}", [
            'item_id' => $item->id,
            'user_id' => $user->id
        ]);
        Auth::login($user->fresh());

        $response = $this->get("/item/{$item->id}");
        $response->assertViewHas('item', function ($item) {
            return $item->favoriteUsers->count() === 1;
        });
        $response->assertSee('fa-solid fa-star');
        $response->assertDontSee('fa-regular fa-star');
        //$response->dump();
    }

    //いいね済みアイコンを再度押下するといいねが解除され、合計値が減少表示される
    public function test_click_icon_again_to_cancel_favorite()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $item = Item::factory()->create();

        $user->favoriteItems()->syncWithoutDetaching([
            'item_id' => $item->id
        ]);
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
        $this->delete("/favorite/{$item->id}", [
            'item_id' => $item->id,
            'user_id' => $user->id
        ]);
        Auth::login($user->fresh());

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
        //$response->dump();
    }
}
