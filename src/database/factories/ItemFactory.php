<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */

    // ItemTest用
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->realText($maxNbChars = 20),
            'brand_name' => $this->faker->optional()->word(),
            'price' => $this->faker->numberBetween(0, 9999999),
            'explain' => $this->faker->realText(),
            'condition' => $this->faker->numberBetween(1, 4),
            'img_path' => 'img/dummy/Armani+Mens+Clock.jpg',
            'status' => 'available'
        ];
    }
}
