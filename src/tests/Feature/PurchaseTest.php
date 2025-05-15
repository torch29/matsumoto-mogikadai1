<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Profile;
use Illuminate\Support\Facades\Auth;

class PurchaseTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    public function test_can_purchase_item_with_card_that_click_button()
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
}
