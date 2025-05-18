<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Comment;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Comment::create([
            'item_id' => 1,
            'user_id' => 2,
            'comment' => 'コメント失礼いたします。保証書などはありますか？'
        ]);

        Comment::create([
            'item_id' => 1,
            'user_id' => 1,
            'comment' => 'はい、お付けできます。
            ご検討よろしくお願いします。'
        ]);

        Comment::factory()->count(8)->create();
    }
}
