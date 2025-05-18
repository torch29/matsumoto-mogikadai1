<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'zip_code' => $this->faker->numerify('###-####'),
            'address' => implode('', [
                $this->faker->prefecture(),
                $this->faker->city(),
                $this->faker->streetAddress(),
            ]),
            'building' => $this->faker->optional(0.7)->secondaryAddress,
        ];
    }
}
