<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Profile;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\Rating;

class RatingTest extends TestCase
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
        $seller = User::factory()->has(Profile::factory())->create([
            'name' => '出品者'
        ]);
        $buyer = User::factory()->has(Profile::factory())->create([
            'name' => '購入者'
        ]);
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

    /* まだ評価がないユーザーの場合、評価は表示されない */
    public function test_do_not_display_star_when_user_not_received_rating()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        //まだ評価がない場合、☆は表示されないことを確認
        $response = $this->get('/mypage/');
        $response->assertDontSee('fa-solid fa-star');
    }

    /* 取引チャットから購入者が評価を送信できる */
    public function test_buyer_can_rating_the_trade()
    {
        //設定から取引中の商品を作成
        $data = $this->createSoldItemWithUsers();

        //取引チャット画面にアクセスし、「取引を完了する」ボタンが表示されていることを確認
        $this->actingAs($data['buyer']);
        $response = $this->get("/mypage/chat/{$data['item']->id}");
        $response->assertSee('取引を完了する');
        //評価を送信する
        $ratingScore = 4;
        $this->post(route('buyer.rating'), [
            'purchase_id' => $data['purchase']->id,
            'reviewer_id' => $data['buyer']->id,
            'reviewee_id' => $data['seller']->id,
            'score' => $ratingScore,
        ]);
        //ratingsテーブルに評価データが登録されたことと、purchasesテーブルのstatusがbuyer_ratedにupdateされたことを確認
        $this->assertDatabaseHas('ratings', [
            'purchase_id' => $data['purchase']->id,
            'reviewer_id' => $data['buyer']->id,
            'reviewee_id' => $data['seller']->id,
            'score' => $ratingScore,
        ]);
        $this->assertDatabaseHas('purchases', [
            'id' => $data['purchase']->id,
            'status' => 'buyer_rated',
        ]);

        //マイページにアクセスし、取引中だった商品が表示されていないことを確認
        $response = $this->get('/mypage');
        $htmlA = $response->getContent();
        $idPos = strpos($htmlA, 'id="tradingItems"');
        $itemPos = strpos($htmlA, $data['item']->name);
        $this->assertFalse($itemPos > $idPos);

        //出品者側でログインし、受けた評価と一致した★が表示されていることを確認
        $this->actingAs($data['seller']);
        $response = $this->get('/mypage');
        $htmlB = $response->getContent();
        preg_match_all('/<span class="star">/', $htmlB, $matches); // empty がついていない <span class="star"> を数える
        $this->assertCount($ratingScore, $matches[0]);
    }

    /* 出品者は購入者の評価前には評価送信できない */
    public function test_seller_can_not_rating_before_buyer_rating()
    {
        //設定から取引中の商品を作成
        $data = $this->createSoldItemWithUsers();

        //出品者でログイン後 取引チャット画面にアクセスし、「取引を完了する」ボタンが表示されていないことを確認
        $this->actingAs($data['seller']);
        $response = $this->get("/mypage/chat/{$data['item']->id}");
        $response->assertDontSee('取引を完了する');
        //評価を送信する
        $ratingScore = 4;
        $this->post(route('seller.rating'), [
            'purchase_id' => $data['purchase']->id,
            'reviewer_id' => $data['seller']->id,
            'reviewee_id' => $data['buyer']->id,
            'score' => $ratingScore,
        ]);
        //ratingsテーブルに評価データが登録されていないことと、purchasesテーブルのstatusがtradingのままであることを確認
        $this->assertDatabaseMissing('ratings', [
            'purchase_id' => $data['purchase']->id,
            'reviewer_id' => $data['seller']->id,
            'reviewee_id' => $data['buyer']->id,
            'score' => $ratingScore,
        ]);
        $this->assertDatabaseHas('purchases', [
            'id' => $data['purchase']->id,
            'status' => 'trading',
        ]);
    }

    /* 出品者は購入者の評価のあとに評価送信できる */
    public function test_seller_can_rating_the_trade()
    {
        //設定から取引中の商品を作成
        $data = $this->createSoldItemWithUsers();
        //購入者が先に評価する
        $this->actingAs($data['buyer']);
        $response = $this->get("/mypage/chat/{$data['item']->id}");
        $sellerReceivedScore = 4;
        $this->post(route('buyer.rating'), [
            'purchase_id' => $data['purchase']->id,
            'reviewer_id' => $data['buyer']->id,
            'reviewee_id' => $data['seller']->id,
            'score' => $sellerReceivedScore,
        ]);

        //出品者でログイン後 取引チャット画面にアクセスし、「取引を完了する」ボタンが表示されていることを確認
        $this->actingAs($data['seller']);
        $response = $this->get("/mypage/chat/{$data['item']->id}");
        $response->assertSee('取引を完了する');
        //出品者が評価を送信する
        $buyerReceivedScore = 2;
        $this->post(route('seller.rating'), [
            'purchase_id' => $data['purchase']->id,
            'reviewer_id' => $data['seller']->id,
            'reviewee_id' => $data['buyer']->id,
            'score' => $buyerReceivedScore,
        ]);
        //ratingsテーブルに評価データが登録されたことと、purchasesテーブルのstatusがcompletedにupdateされたことを確認
        $this->assertDatabaseHas('ratings', [
            'purchase_id' => $data['purchase']->id,
            'reviewer_id' => $data['seller']->id,
            'reviewee_id' => $data['buyer']->id,
            'score' => $buyerReceivedScore,
        ]);
        $this->assertDatabaseHas('purchases', [
            'id' => $data['purchase']->id,
            'status' => 'completed',
        ]);

        //マイページにアクセスし、取引中だった商品が表示されていないことを確認
        $response = $this->get('/mypage');
        $htmlA = $response->getContent();
        $idPos = strpos($htmlA, 'id="tradingItems"');
        $itemPos = strpos($htmlA, $data['item']->name);
        $this->assertFalse($itemPos > $idPos);

        //購入者側でログインし、受けた評価と一致した★が表示されていることを確認
        $this->actingAs($data['buyer']);
        $response = $this->get('/mypage');
        $htmlB = $response->getContent();
        preg_match_all('/<span class="star">/', $htmlB, $matches); // empty がついていない <span class="star"> を数える
        $this->assertCount($buyerReceivedScore, $matches[0]);
    }

    /* マイページの評価は小数点以下を四捨五入し整数に対応した☆で表示される */
    public function test_average_rating_is_rounded_and_displayed()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // $user1の得た評価の平均が 3.3になるケースを作成
        Rating::factory()->create([
            'reviewee_id' => $user1->id,
            'score' => 3
        ]);
        Rating::factory()->create([
            'reviewee_id' => $user1->id,
            'score' => 4,
        ]);
        Rating::factory()->create([
            'reviewee_id' => $user1->id,
            'score' => 3,
        ]);

        //マイページにアクセスし、ぬりつぶされた★が3つ、空の☆が２つ表示されていることを確認
        $response1 = $this->actingAs($user1)->get(route('mypage'));
        $response1->assertViewHas('roundedScore', 3);
        $filledStars1 = substr_count($response1->getContent(), '<span class="star">');
        $this->assertEquals(3, $filledStars1); // 3.3 → round() = 3
        $emptyStars1 = substr_count($response1->getContent(), '<span class="star empty">');
        $this->assertEquals(2, $emptyStars1);

        // $user2の得た評価の平均が 3.5になるケースを作成
        Rating::factory()->create([
            'reviewee_id' => $user2->id,
            'score' => 3,
        ]);
        Rating::factory()->create([
            'reviewee_id' => $user2->id,
            'score' => 4,
        ]);
        $response2 = $this->actingAs($user2)->get(route('mypage'));
        $response2->assertViewHas('roundedScore', 4);
        $filledStars2 = substr_count($response2->getContent(), '<span class="star">');
        $this->assertEquals(4, $filledStars2); // 3.5 → round() = 4
        $emptyStars2 = substr_count($response2->getContent(), '<span class="star empty">');
        $this->assertEquals(1, $emptyStars2);
    }
}
