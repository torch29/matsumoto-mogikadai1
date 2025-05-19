<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
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

    //ログイン済みユーザーはコメント送信ができる。コメント数が増加表示される
    public function test_can_comment_that_login_user()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $item = Item::factory()->create();

        //商品詳細画面にアクセスしたとき、コメントは0件であることを確認
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

        //コメントを投稿、commentsテーブルに保存されることを確認
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

        //商品詳細画面にて、コメントの内容が反映されており、コメント件数が1件に増えていることを確認
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

    //未ログインログインユーザーはコメントできない
    public function test_can_not_comment_guest_user()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        //未ログインユーザーが商品詳細ページにアクセスする。コメントが0件であることを確認
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

        //未ログインユーザーがコメントを投稿しようとする
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

        //コメントが反映されておらず、コメント件数も0件のままであることを確認
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

    //コメントが入力されていない場合、バリデーションメッセージが表示される
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

    //コメントが255文字以上の場合、バリデーションメッセージが表示される
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
