<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FavoriteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $favorites = [
            [
                'item_id' => 1,
                'user_id' => 2
            ],
            [
                'item_id' => 1,
                'user_id' => 3,
            ],
            [
                'item_id' => 2,
                'user_id' => 2,
            ],
        ];

        foreach ($favorites as $favorite) {
            DB::table('favorites')->insert($favorite);
        }
    }
}
