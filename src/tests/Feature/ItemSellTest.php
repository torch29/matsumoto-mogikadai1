<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Profile;
use App\Models\Category;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ItemSellTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    //商品を出品すると、商品の各項目が保存されている
    public function test_can_save_user_sell_items_data()
    {
        //ユーザーを作成しログイン状態にする
        $user = User::factory()
            ->has(Profile::factory())
            ->create();
        $this->actingAs($user);
        Storage::fake('public');
        $imgFile = UploadedFile::fake()->create('fake_cup.jpg', 100, 'image/jpeg');

        //出品画面にアクセスし、ユーザーが出品するpostアクション
        $response = $this->get('/sell');
        Category::insert([
            ['id' => 3, 'content' => 'インテリア'],
            ['id' => 10, 'content' => 'キッチン']
        ]);
        $this->post("/sell?user_id={$user->id}", [
            'name' => 'コーヒーカップ',
            'brand_name' => 'Coffee',
            'price' => 2500,
            'explain' => '白い磁器製のシンプルなカップです',
            'condition' => 2,
            'category_ids' => [3, 10],
            'img_path' => $imgFile,
        ]);
        //データベースに出品したデータと同じものが保存されていることを確認
        $this->assertDatabaseHas('items', [
            'user_id' => $user->id,
            'name' => 'コーヒーカップ',
            'brand_name' => 'Coffee',
            'price' => 2500,
            'explain' => '白い磁器製のシンプルなカップです',
            'condition' => 2,
        ]);
    }
}
