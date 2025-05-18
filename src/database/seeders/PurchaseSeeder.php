<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\Profile;
use App\Models\Purchase;

class PurchaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $profile = Profile::where('user_id', 1)->first();
        $item1 = Item::find(2);
        Purchase::create([
            'user_id' => 1,
            'item_id' => $item1->id,
            'payment' => 'card',
            'zip_code' => $profile->zip_code,
            'address' => $profile->address,
            'building' => $profile->building,
        ]);
        $item1->update(['status' => 'sold']);

        $item2 = Item::find(7);
        Purchase::create([
            'user_id' => 1,
            'item_id' => $item2->id,
            'payment' => 'konbini',
            'zip_code' => '000-1234',
            'address' => '沖縄県那覇市012',
            'building' => '住所変更アパート55',
        ]);
        $item2->update(['status' => 'pending']);
    }
}
