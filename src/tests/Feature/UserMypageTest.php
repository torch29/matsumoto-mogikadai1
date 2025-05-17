<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Profile;
use App\Models\Purchase;

class UserMypageTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    public function test_show_users_information()
    {
        $user = User::factory()
            ->has(Profile::factory([
                'profile_img' => 'images/user/icon.png',
            ]))
            ->create([
                'name' => 'テスト　ユーザー',
            ]);
        $this->actingAs($user);

        //テスト　ユーザーが出品した商品を2件作成
        $userSoldItem1 = Item::factory()->create([
            'user_id' => $user->id,
            'name' => 'テストさんが出品した商品',
        ]);
        $userSoldItem2 = Item::factory()->create([
            'user_id' => $user->id,
            'name' => 'テスト出品商品２'
        ]);

        //テスト　ユーザーが購入した商品を2件作成
        $purchasedItem1 = Item::factory()->create([
            'name' => 'テストさんが購入した商品',
            'status' => 'sold'
        ]);
        Purchase::create([
            'user_id' => $user->id,
            'item_id' => $purchasedItem1->id,
            'payment' => 'card',
            'zip_code' => $user->profile->zip_code,
            'address' => $user->profile->address,
        ]);
        $purchasedItem2 = Item::factory()->create([
            'name' => 'テスト購入商品２',
            'status' => 'pending'
        ]);
        Purchase::create([
            'user_id' => $user->id,
            'item_id' => $purchasedItem2->id,
            'payment' => 'konbini',
            'zip_code' => $user->profile->zip_code,
            'address' => $user->profile->address,
        ]);

        //マイページにアクセスし、ユーザーアイコン、ユーザー名、出品した商品名、購入した商品名が表示されていることを確認
        $response = $this->get('mypage');
        $response->assertViewIs('user.mypage');

        $response->assertSee('images/user/icon.png');
        $response->assertSee('テスト　ユーザー');
        $response->assertSeeInOrder([
            'id="sellItems"',
            'テストさんが出品した商品',
            'テスト出品商品２'
        ], false);
        $response->assertSeeInOrder([
            'id="purchasedItems"',
            'テストさんが購入した商品',
            'テスト購入商品２'
        ], false);
        //$response->dump();
    }

    public function test_show_users_initial_profile_data()
    {
        //ユーザー作成し、プロフィールの初期値を設定
        $user = User::factory()
            ->has(Profile::factory([
                'profile_img' => 'images/user/icon.png',
                'zip_code' => '000-1234',
                'address' => '北海道札幌市123',
                'building' => 'ダミーマンション'
            ]))
            ->create([
                'name' => 'テスト　ユーザー',
            ]);
        $this->actingAs($user);

        //プロフィール編集ページにアクセスし、初期値が表示されていることを確認
        $response = $this->get('/mypage/profile');
        $response->assertViewIs('user.profile');
        $response->assertSeeInOrder([
            'テスト　ユーザー',
            '000-1234',
            '北海道札幌市123',
            'ダミーマンション'
        ], false);
        //$response->dump();
    }
}
