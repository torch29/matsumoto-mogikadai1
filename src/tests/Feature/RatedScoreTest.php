<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Profile;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\Rating;


class RatedScoreTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use RefreshDatabase;

    /* マイページの評価は小数点以下を四捨五入し整数に対応した☆で表示される */
    public function test_average_rated_score_is_rounded_and_displayed()
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
