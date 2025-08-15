<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Profile;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\Chat;
use App\Models\PurchaseUserRead;

class ChatNotifyInMypageTest extends TestCase
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
        $userA = User::factory()->has(Profile::factory())->create();
        $userB = User::factory()->has(Profile::factory())->create();
        $item1 = Item::factory()->create([
            'user_id' => $userA->id, // 出品者
            'status' => 'sold',
        ]);
        $item2 = Item::factory()->create([
            'user_id' => $userB->id, // 出品者
            'status' => 'sold',
        ]);
        $purchase1 = Purchase::factory()->create([
            'user_id' => $userB->id, // 購入者
            'item_id' => $item1->id,
        ]);
        $purchase2 = Purchase::factory()->create([
            'user_id' => $userA->id, // 購入者
            'item_id' => $item2->id,
        ]);

        return [
            'userA' => $userA,
            'userB' => $userB,
            'purchase1' => $purchase1,
            'purchase2' => $purchase2
        ];
    }

    public function test_unread_chat_counts_for_user_returns_correct_values()
    {
        //設定から取引中の商品を作成
        $data = $this->createSoldItemWithUsers();

        // purchase1に対して送信者が購入者（userB）のチャット3件作成
        $chat1 = Chat::factory()->create([
            'purchase_id' => $data['purchase1']->id,
            'sender_id' => $data['userB']->id,
            'created_at' => now()->subMinute(10),
        ]);
        $chat2 = Chat::factory()->create([
            'purchase_id' => $data['purchase1']->id,
            'sender_id' => $data['userB']->id,
            'created_at' => now()->subMinutes(5),
        ]);
        $chat3 = Chat::factory()->create([
            'purchase_id' => $data['purchase1']->id,
            'sender_id' => $data['userB']->id,
            'created_at' => now(),
        ]);

        //4分前に既読のデータ設定
        PurchaseUserRead::create([
            'purchase_id' => $data['purchase1']->id,
            'user_id' => $data['userA']->id,
            'last_read_at' => now()->subMinutes(7), // chat2, chat3が未読となる
        ]);

        // purchase2に対して送信者が出品者（userB）のチャット作成
        Chat::factory()->count(2)->create([
            'purchase_id' => $data['purchase2']->id,
            'sender_id' => $data['userB']->id,
            'created_at' => now()->subMinutes(1),
        ]);
        PurchaseUserRead::create([
            'purchase_id' => $data['purchase2']->id,
            'user_id' => $data['userA']->id,
            'last_read_at' => now()->subHours(1),
        ]);

        //unreadCountsForUserを実行する
        $result = PurchaseUserRead::unreadCountsForUser(
            $data['userA']->id,
            [$data['purchase1']->id, $data['purchase2']->id]
        );

        $this->assertEquals(2, $result[$data['purchase1']->id]); // chat2, chat3が未読
        $this->assertEquals(2, $result[$data['purchase2']->id]); // 2件とも未読
    }

    /* マイページに取引チャットの未読件数が表示されている */
    public function test_display_unread_chat_counts_in_mypage()
    {
        $data = $this->createSoldItemWithUsers();

        // --- purchase1に 未読2件作成 ---
        Chat::factory()->create([
            'purchase_id' => $data['purchase1']->id,
            'sender_id' => $data['userB']->id,
            'created_at' => now()->subMinutes(5),
        ]);
        Chat::factory()->create([
            'purchase_id' => $data['purchase1']->id,
            'sender_id' => $data['userB']->id,
            'created_at' => now(),
        ]);
        PurchaseUserRead::create([
            'purchase_id' => $data['purchase1']->id,
            'user_id' => $data['userA']->id,
            'last_read_at' => now()->subMinutes(10),
        ]);

        // --- purchase2に 未読2件作成 ---
        Chat::factory()->create([
            'purchase_id' => $data['purchase2']->id,
            'sender_id' => $data['userB']->id,
            'created_at' => now()->subMinutes(2),
        ]);
        Chat::factory()->create([
            'purchase_id' => $data['purchase2']->id,
            'sender_id' => $data['userB']->id,
            'created_at' => now()->subMinute(),
        ]);
        PurchaseUserRead::create([
            'purchase_id' => $data['purchase2']->id,
            'user_id' => $data['userA']->id,
            'last_read_at' => now()->subHours(1),
        ]);

        //マイページにアクセスし、未読件数が通知表示されていることを確認
        $response = $this->actingAs($data['userA']);
        $response = $this->get('/mypage');
        $response->assertSeeInOrder([
            '<span class="notify-badge">',
            '2',
            '</span>'
        ], false); //falseでHTMLエスケープを無効にする
        //取引中の商品タブのタイトルに、未読の合計件数が表示されていることを確認
        $response->assertSeeInOrder([
            '取引中の商品',
            'notify--count',
            4,
            'mypage-content'
        ]);
    }

    /* 未読チャットがない場合はなにも件数表示されない */
    public function test_do_not_display_unread_count_when_zero_in_mypage()
    {
        $user = User::factory()->create();

        //ユーザーがマイページにアクセスし、件数表示されていないことを確認
        $response = $this->actingAs($user)
            ->get('mypage');
        $response->assertDontSee('<span class="notify--count">', false);
    }
}
