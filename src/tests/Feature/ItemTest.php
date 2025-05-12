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

    public function test_display_purchased_item()
    {
        $items = Item::factory()->count(5)->create();
        $items->shuffle()->take(2)->each(function ($item) {
            $item->update(['status' => 'sold']);
        });

        $response = $this->get('/');
        $response->assertSeeText('sold');
    }

    public function test_not_display_user_sold_item()
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
}
