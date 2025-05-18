<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Profile;

class ItemSellTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    public function test_can_save_user_sold_items_data()
    {
        $user = User::factory()
            ->has(Profile::factory())
            ->create();
        $this->actingAs($user);

        $response = $this->get('/sell');
        $this->post("/sell?user_id={$user->id}", [
            'name' => 'コーヒーカップ',
            'brand' => 'Coffee',
            'price' => 2500,
            'explain' => '白い磁器製のシンプルなカップです',
            'condition' => 2,
            'img_path' => '',
        ]);
        $this->assertDatabaseHas('items', [
            'user_id' => $user->id,
            'name' => 'コーヒーカップ',
            'brand' => 'Coffee',
            'price' => 2500,
            'explain' => '白い磁器製のシンプルなカップです',
            'condition' => 2,
        ]);
    }
}
