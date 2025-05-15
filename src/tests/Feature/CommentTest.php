<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;

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
        $response->assertSeeText('コメント ( 0 )');
        $response->assertSeeInOrder([
            'fa-comment',
            0,
        ], false);

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
        $response->assertSeeInOrder([
            'fa-comment',
            1,
        ], false);
        $response->assertSee($user->name);
        $response->assertSee('箱はありますか');
        $response->assertSeeText('コメント ( 1 )');
        //$response->dump();
    }

    public function test_can_not_comment_guest_user()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this->assertGuest();
        $response = $this->get("/item/{$item->id}");
        $response->assertViewIs('item.detail');
        $response->assertViewHas('item', function ($item) {
            return $item->comments->count() === 0;
        });
        $response->assertSeeText('コメント ( 0 )');
        $response->assertSeeInOrder([
            'fa-comment',
            0,
        ], false);
        $this->post('/comment', [
            'item_id' => $item->id,
            'user_id' => $user->id,
            'comment' => '箱はありますか'
        ]);
        $this->assertDatabaseMissing('comments', [
            'item_id' => $item->id,
            'user_id' => $user->id,
            'comment' => '箱はありますか'
        ]);

        $response = $this->get("/item/{$item->id}");
        $response->assertViewHas('item', function ($item) {
            return $item->comments->count() === 0;
        });
        $response->assertSeeText('コメント ( 0 )');
        $response->assertSeeInOrder([
            'fa-comment',
            0,
        ], false);
    }

    public function test_show_message_when_empty_comment()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $item = Item::factory()->create();

        $response = $this->get("/item/{$item->id}");
        $response->assertViewIs('item.detail');
        $this->post('/comment', [
            'item_id' => $item->id,
            'user_id' => $user->id,
            'comment' => ''
        ]);
        $response->assertSessionHasErrors([
            'comment' => 'コメントを入力してください'
        ]);
    }

    public function test_show_message_when_over_255_characters_comment()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $item = Item::factory()->create();

        $response = $this->get("/item/{$item->id}");
        $response->assertViewIs('item.detail');
        $longComment = str_repeat('あ', 256);
        $this->post('/comment', [
            'item_id' => $item->id,
            'user_id' => $user->id,
            'comment' => $longComment
        ]);
        $response->assertSessionHasErrors([
            'comment' => 'コメント内容は255文字以下で入力してください'
        ]);
        //$response->dump();
    }
}
