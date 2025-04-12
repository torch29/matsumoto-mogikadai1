<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $param = [
            'content' => 'ファッション'
        ];
        DB::table('categories')->insert($param);

        $param = [
            'content' => '家電'
        ];
        DB::table('categories')->insert($param);

        $param = [
            'content' => 'インテリア'
        ];
        DB::table('categories')->insert($param);

        $param = [
            'content' => 'レディース'
        ];
        DB::table('categories')->insert($param);

        $param = [
            'content' => 'メンズ'
        ];
        DB::table('categories')->insert($param);

        $param = [
            'content' => 'コスメ'
        ];
        DB::table('categories')->insert($param);

        $param = [
            'content' => '本'
        ];
        DB::table('categories')->insert($param);

        $param = [
            'content' => 'ゲーム'
        ];
        DB::table('categories')->insert($param);

        $param = [
            'content' => 'スポーツ'
        ];
        DB::table('categories')->insert($param);

        $param = [
            'content' => 'キッチン'
        ];
        DB::table('categories')->insert($param);

        $param = [
            'content' => 'ハンドメイド'
        ];
        DB::table('categories')->insert($param);

        $param = [
            'content' => 'アクセサリー'
        ];
        DB::table('categories')->insert($param);

        $param = [
            'content' => 'おもちゃ'
        ];
        DB::table('categories')->insert($param);

        $param = [
            'content' => 'ベビー・キッズ'
        ];
        DB::table('categories')->insert($param);
    }
}
