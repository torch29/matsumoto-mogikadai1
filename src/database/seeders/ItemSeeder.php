<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = [
            [
                'user_id' => 3,
                'name' => '腕時計',
                'brand_name' => 'Almani',
                'price' => 15000,
                'explain' => 'スタイリッシュなデザインのメンズ腕時計',
                'condition' => 1,
                'img_path' => 'img/dummy/Armani+Mens+Clock.jpg',
                'status' => 'available',
            ],
            [
                'user_id' => 1,
                'name' => 'HDD',
                'brand_name' => '',
                'price' => 5000,
                'explain' => '高速で信頼性の高いハードディスク',
                'condition' => 2,
                'img_path' => 'img/dummy/HDD+Hard+Disk.jpg',
                'status' => 'available',
            ],
            [
                'user_id' => 1,
                'name' => 'タマネギ3束',
                'brand_name' => '',
                'price' => 300,
                'explain' => '新鮮な玉ねぎ3束のセット',
                'condition' => 3,
                'img_path' => 'img/dummy/iLoveIMG+d.jpg',
                'status' => 'sold',
            ],
            [
                'user_id' => 4,
                'name' => '革靴',
                'brand_name' => '',
                'price' => 4000,
                'explain' => 'クラシックなデザインの革靴',
                'condition' => 4,
                'img_path' => 'img/dummy/Leather+Shoes+Product+Photo.jpg',
                'status' => 'available',
            ],
            [
                'user_id' => 5,
                'name' => 'ノートPC',
                'brand_name' => 'Pamasonic',
                'price' => 45000,
                'explain' => '高性能なノートパソコン',
                'condition' => 1,
                'img_path' => 'img/dummy/Living+Room+Laptop.jpg',
                'status' => 'sold',
            ],
            [
                'user_id' => 3,
                'name' => 'マイク',
                'brand_name' => 'ノンマン',
                'price' => 8000,
                'explain' => '高音質のレコーディング用マイク',
                'condition' => 2,
                'img_path' => 'img/dummy/Music+Mic+4632231.jpg',
                'status' => 'available',
            ],
            [
                'user_id' => 4,
                'name' => 'ショルダーバッグ',
                'brand_name' => 'Gutti',
                'price' => 3500,
                'explain' => 'おしゃれなショルダーバッグ',
                'condition' => 2,
                'img_path' => 'img/dummy/Purse+fashion+pocket.jpg',
                'status' => 'available',
            ],
            [
                'user_id' => 3,
                'name' => 'タンブラー',
                'brand_name' => 'サムス',
                'price' => 500,
                'explain' => '使いやすいタンブラー',
                'condition' => 4,
                'img_path' => 'img/dummy/Tumbler+souvenir.jpg',
                'status' => 'available',
            ],
            [
                'user_id' => 3,
                'name' => 'コーヒーミル',
                'brand_name' => 'コリタ',
                'price' => 4000,
                'explain' => '手動のコーヒーミル',
                'condition' => 1,
                'img_path' => 'img/dummy/Waitress+with+Coffee+Grinder.jpg',
                'status' => 'sold',
            ],
            [
                'user_id' => 4,
                'name' => 'メイクセット',
                'brand_name' => '',
                'price' => 2500,
                'explain' => '便利なメイクアップセット',
                'condition' => 2,
                'img_path' => 'img/dummy/外出メイクアップセット.jpg',
                'status' => 'available',
            ],
        ];

        foreach ($items as $item) {
            DB::table('items')->insert($item);
        }
    }
}
