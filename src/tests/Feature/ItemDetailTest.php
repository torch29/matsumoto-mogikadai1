<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Profile;
use Illuminate\Support\Facades\Auth;

class ItemDetailTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    public function test_display_item_detail()
    {
        $user = User::factory()->create([
            'name' => 'テスト　ユーザー'
        ]);
        $user->profile()->create(['profile_img' => 'images/user/icon.png']);
        $item = Item::factory()->create([
            'user_id' => $user->id,
            'name' => 'コーヒーカップ',
            'brand_name' => 'ウェッジフォレスト',
            'price' => 2000,
            'explain' => 'シンプルなカラー・デザインのカップ',
            'condition' => 2,
            'img_path' => 'images/item/fake.jpg',
        ]);
        Category::insert([
            ['id' => 2, 'content' => '家電'],
            ['id' => 3, 'content' => 'インテリア'],
            ['id' => 10, 'content' => 'キッチン']
        ]);
        $user->favoriteItems()->syncWithoutDetaching([
            'user_id' => $user->id,
            'item_id' => $item->id
        ]);
        $item->categories()->attach([2, 3, 10]);
        Comment::create([
            'item_id' => $item->id,
            'user_id' => $user->id,
            'comment' => 'ソーサーはありますか'
        ]);

        $this->assertGuest();
        $response = $this->get("/item/{$item->id}");
        $response->assertViewIs('item.detail');
        $response->assertSee('コーヒーカップ');
        $response->assertSee('ウェッジフォレスト');
        $response->assertSeeText('￥2,000（税込）');
        $response->assertSee('シンプルなカラー・デザインのカップ');
        $response->assertSee('目立った傷や汚れなし');
        $response->assertSee('家電');
        $response->assertSee('インテリア');
        $response->assertSee('キッチン');
        $response->assertSee('images/item/fake.jpg');
        //コメント欄
        $response->assertSee('テスト　ユーザー');
        $response->assertSee('images/user/icon.png');
        $response->assertSee('ソーサーはありますか');
        $response->assertSeeText('コメント ( 1 )');
        $response->assertViewHas('item', function ($item) {
            return $item->name === 'コーヒーカップ'
                && $item->price === 2000
                && $item->comments->count() === 1
                && $item->comments->pluck('comment')->contains('ソーサーはありますか')
                && $item->favoriteUsers->count() === 1;
        });
    }

    public function test_display_several_categories_stored_for_item()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create([
            'user_id' => $user->id,
            'name' => 'コーヒーカップ',
            'brand_name' => 'ウェッジフォレスト',
            'price' => 2000,
            'explain' => 'シンプルなカラー・デザインのカップ',
            'condition' => 2,
            'img_path' => 'images/fake.jpg',
        ]);
        Category::insert([
            ['id' => 2, 'content' => '家電'],
            ['id' => 3, 'content' => 'インテリア'],
            ['id' => 10, 'content' => 'キッチン']
        ]);
        $item->categories()->attach([2, 3, 10]);

        $this->assertGuest();
        $response = $this->get("/item/{$item->id}");
        $response->assertViewIs('item.detail');
        $response->assertSee('家電');
        $response->assertSee('インテリア');
        $response->assertSee('キッチン');
        $response->assertViewHas('item', function ($item) {
            return $item->categories->count() === 3 &&
                $item->categories->pluck('content')->contains('家電')
                && $item->categories->pluck('content')->contains('インテリア')
                && $item->categories->pluck('content')->contains('キッチン');
        });
    }

    public function test_save_favorite_items_that_user_press_icon()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $item = Item::factory()->create();

        $response = $this->get("/item/{$item->id}");
        $response->assertViewHas('item', function ($item) {
            return $item->favoriteUsers->count() === 0;
        });
        $this->post("/favorite/{$item->id}", [
            'item_id' => $item->id,
            'user_id' => $user->id
        ]);
        $this->assertDatabaseHas('favorites', [
            'item_id' => $item->id,
            'user_id' => $user->id
        ]);
        $response = $this->get("/item/{$item->id}");
        $response->assertViewHas('item', function ($item) {
            return $item->favoriteUsers->count() === 1;
        });
    }

    public function test_change_icon_color_that_saved_user_favorite()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $item = Item::factory()->create();

        $response = $this->get("/item/{$item->id}");
        $response->assertSee('fa-regular fa-star');
        $response->assertDontSee('fa-solid fa-star');
        $this->post("/favorite/{$item->id}", [
            'item_id' => $item->id,
            'user_id' => $user->id
        ]);
        Auth::login($user->fresh());

        $response = $this->get("/item/{$item->id}");
        $response->assertViewHas('item', function ($item) {
            return $item->favoriteUsers->count() === 1;
        });
        $response->assertSee('fa-solid fa-star');
        $response->assertDontSee('fa-regular fa-star');
        //$response->dump();
    }
}


/*
コメント投稿のテスト用
        $response = $this->post('/comment', ['item_id' => $item->id, 'user_id' => $user->id, 'comment' => 'ソーサーはありますか']);
        */