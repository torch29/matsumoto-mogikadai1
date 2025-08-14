<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

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
            'item_id' => rand(1, 5),
            'user_id' => rand(2, 3),
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
