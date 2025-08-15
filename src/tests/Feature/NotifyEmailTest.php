<?php

namespace Tests\Feature;

use App\Mail\NotifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;

class NotifyEmailTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    /* 購入者が評価送信後、出品者に通知のメールが送信される */
    public function test_mail_is_sent_when_buyer_rates()
    {
        Mail::fake();

        $purchase = Purchase::factory()->has(Item::factory()->has(User::factory()), 'purchasedItem') // 出品者
            ->create([
                'user_id' => User::factory()->create()->id, //購入者
                'status' => 'buyer_rated',
            ]);

        $buyer = $purchase->purchasedUser;
        $seller = $purchase->purchasedItem->users;

        $this->actingAs($buyer)
            ->post(route('buyer.rating'), [
                'purchase_id' => $purchase->id,
                'reviewer_id' => $buyer->id,
                'reviewee_id' => $seller->id,
                'score' => 5,
            ])
            ->assertStatus(302);

        // 出品者宛てにメールが送信されていることを確認
        Mail::assertSent(NotifyEmail::class, function ($mail) use ($purchase, $seller) {
            return $mail->hasTo($seller->email) &&
                $mail->purchase->id === $purchase->id;
        });
        // 購入者にはメール送信されていないことを確認
        Mail::assertNotSent(NotifyEmail::class, function ($mail) use ($buyer) {
            return $mail->hasTo($buyer->email);
        });
    }
}
