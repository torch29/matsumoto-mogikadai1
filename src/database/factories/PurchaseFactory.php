<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Item;
use App\Models\User;

class PurchaseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    /* Test用 */
    public function definition()
    {
        return [
            'item_id' => Item::factory(),
            'user_id' => User::factory(),
            'payment' => 'card',
            'zip_code' => $this->faker->numerify('###-####'),
            'address' => implode('', [
                $this->faker->prefecture(),
                $this->faker->city(),
                $this->faker->streetAddress(),
            ]),
            'status' => 'trading',
        ];
    }
}
