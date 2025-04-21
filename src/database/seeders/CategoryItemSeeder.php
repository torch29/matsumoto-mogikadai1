<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoryItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $itemCategories = [

            1 => [1, 5, 12],
            2 => [2],
            3 => [10],
            4 => [5, 1],
            5 => [2],
            6 => [2],
            7 => [1],
            8 => [10],
            9 => [10, 3],
            10 => [1, 6]
        ];

        foreach ($itemCategories as $itemId => $categoryIds) {
            foreach ($categoryIds as $categoryId) {
                DB::table('category_item')->insert([
                    'item_id' => $itemId,
                    'category_id' => $categoryId,
                ]);
            }
        }
    }
}
