<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {



        User::create(
            [
                'name' => 'テスト　ユーザー',
                'email' => 'test@example.com',
                'password' => Hash::make('12345678'),
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ]
        );
        User::create([
            'name' => 'あいい　うえお',
            'email' => 'test2@example.com',
            'password' => Hash::make('12345678'),
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ]);
        User::create([
            'name' => 'かきく　くけこ',
            'email' => 'test3@example.com',
            'password' => Hash::make('12345678'),
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ]);
    }
}
