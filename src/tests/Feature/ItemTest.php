<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;

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

    public function test_show_users_favorite_items()
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
}
