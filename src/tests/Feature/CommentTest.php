<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;

class CommentTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use RefreshDatabase;

    public function test_can_comment_that_login_user()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $item = Item::factory()->create();

        $response = $this->get("/item/{$item->id}");
        $response->assertViewIs('item.detail');
        $response->assertViewHas('item', function ($item) {
            return $item->comments->count() === 0;
        });

        $this->post('/comment', [
            'item_id' => $item->id,
            'user_id' => $user->id,
            'comment' => '箱はありますか'
        ]);
        $this->assertDatabaseHas('comments', [
            'item_id' => $item->id,
            'user_id' => $user->id,
            'comment' => '箱はありますか'
        ]);
        $response = $this->get("/item/{$item->id}");
        $response->assertViewHas('item', function ($item) {
            return $item->comments->count() === 1;
        });
        $response->assertSee('箱はありますか');
        $response->assertSeeText();
        $response->dump();
    }
}
