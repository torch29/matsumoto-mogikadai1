<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Profile;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\Chat;

class ChatMessageTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use RefreshDatabase;

    /* 「取引中」の商品を作るため、ユーザーが出品した商品が売れた状態にする設定 */
    private function createSoldItemWithUsers()
    {
        $seller = User::factory()->has(Profile::factory())->create();
        $buyer = User::factory()->has(Profile::factory())->create();
        $item = Item::factory()->create([
            'user_id' => $seller->id,
            'status' => 'sold',
        ]);
        $purchase = Purchase::factory()->create([
            'user_id' => $buyer->id,
            'item_id' => $item->id,
        ]);

        return [
            'seller' => $seller,
            'buyer' => $buyer,
            'item' => $item,
            'purchase' => $purchase,
        ];
    }

    /* チャットを送信するとchatsテーブルに保存される */
    public function test_can_send_message_in_chat()
    {
        //設定から取引中の商品を作成
        $data = $this->createSoldItemWithUsers();

        //取引チャットにアクセスしてチャットを送信する
        $this->actingAs($data['seller']);
        $response = $this->get("/mypage/chat/{$data['item']->id}");
        $sendMessage = str_repeat('あ', 400); //上限の400文字
        $this->post('/mypage/chat', [
            'purchase_id' => $data['purchase']->id,
            'sender_id' => $data['seller']->id,
            'message' => $sendMessage,
        ]);

        //データベースに登録されていて、ビューにも反映されていることを確認
        $this->assertDatabaseHas('chats', [
            'purchase_id' => $data['purchase']->id,
            'sender_id' => $data['seller']->id,
            'message' => $sendMessage,
        ]);
        $response = $this->get("/mypage/chat/{$data['item']->id}");
        $response->assertSee($sendMessage);
        $response->assertViewHas(
            'chats',
            fn($records) =>
            $records->pluck('message')->contains($sendMessage)
        );
    }

    /* チャット本文が未入力の場合、バリデーションメッセージが表示される */
    public function test_show_message_for_empty_message()
    {
        $data = $this->createSoldItemWithUsers();

        $this->actingAs($data['seller']);
        $response = $this->get("/mypage/chat/{$data['item']->id}");
        $this->post('/mypage/chat', [
            'purchase_id' => $data['purchase']->id,
            'sender_id' => $data['seller']->id,
            'message' => '',
        ]);
        $response->assertSessionHasErrors([
            'message' => '本文を入力してください'
        ]);
    }

    /* チャット本文が400文字以上の場合、バリデーションメッセージが表示される */
    public function test_show_message_for_comment_over_400_characters()
    {
        $data = $this->createSoldItemWithUsers();

        $this->actingAs($data['seller']);
        $response = $this->get("/mypage/chat/{$data['item']->id}");
        $longMessage = str_repeat('あ', 401);
        $this->post('/mypage/chat', [
            'purchase_id' => $data['purchase']->id,
            'sender_id' => $data['seller']->id,
            'message' => $longMessage,
        ]);
        $response->assertSessionHasErrors([
            'message' => '本文は400文字以下で入力してください'
        ]);
    }

    /* 取引相手から受け取ったちゃっとを確認できる */
    public function test_display_message_that_received_chat()
    {
        $data = $this->createSoldItemWithUsers();

        //取引相手からのチャットを作成
        $receivedChat = Chat::factory()->create([
            'purchase_id' => $data['purchase'],
            'sender_id' => $data['buyer'],
        ]);

        //ユーザーが取引チャット画面にアクセスし、受け取ったメッセージを確認できる
        $this->actingAs($data['seller']);
        $response = $this->get("/mypage/chat/{$data['item']->id}");
        $response->assertSee($receivedChat->message);
    }

    /* ユーザーは自分の送ったチャット内容を編集することができる */
    public function test_user_can_edit_messages_that_own_sent()
    {
        $data = $this->createSoldItemWithUsers();

        //チャット画面にアクセスし、チャットを送信
        $this->actingAs($data['seller']);
        $response = $this->get("/mypage/chat/{$data['item']->id}");
        $sendMessage = str_repeat('あ', 400); //上限の400文字
        $this->post('/mypage/chat', [
            'purchase_id' => $data['purchase']->id,
            'sender_id'   => $data['seller']->id,
            'message'     => $sendMessage,
        ]);
        $chat = Chat::latest()->first();

        //編集リンクをクリックし、編集内容を送信
        $response = $this->get("/mypage/chat/{$data['item']->id}?edit={$chat->id}#chat-{$chat->id}");
        $response->assertViewIs('item.trading.chat_sell_user');
        $editMessage = '編集テストチャット';
        $response = $this->patch('/mypage/chat/update', [
            'id' => $chat->id,
            'message' => $editMessage,
            'transitionId' => $data['item']->id,
        ]);
        //データベースに登録され、ビューにも反映されていることを確認
        $this->assertDatabaseHas('chats', [
            'id' => $chat->id,
            'message' => $editMessage,
        ]);
        $response = $this->get("/mypage/chat/{$data['item']->id}");
        $response->assertSee($editMessage);
    }

    /* ユーザーは、自分が送ったチャットメッセージを削除することができる */
    public function test_user_can_delete_messages_that_own_sent()
    {
        $data = $this->createSoldItemWithUsers();

        //チャット画面にアクセスし、チャットを送信
        $this->actingAs($data['seller']);
        $response = $this->get("/mypage/chat/{$data['item']->id}");
        $sendMessage = str_repeat('あ', 400); //上限の400文字
        $this->post('/mypage/chat', [
            'purchase_id' => $data['purchase']->id,
            'sender_id'   => $data['seller']->id,
            'message'     => $sendMessage,
        ]);
        $chat = Chat::latest()->first();
        $response = $this->delete('/mypage/chat/delete', [
            'id' => $chat->id,
            'transitionId' => $data['item']->id,
        ]);
        $response = $this->get("/mypage/chat/{$data['item']->id}");
        $response->assertDontSee($sendMessage);
        $response = $this->assertDatabaseMissing(
            'chats',
            [
                'id' => $chat->id,
                'message' => $sendMessage
            ]
        );
    }
}
