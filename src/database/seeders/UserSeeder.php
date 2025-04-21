<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'テスト　ユーザー',
            'email' => 'test@example.com',
            'password' => Hash::make('12345678')
        ]);

        User::factory()->count(4)->create();
    }
}
