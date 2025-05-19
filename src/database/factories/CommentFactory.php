<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => rand(2, 5),
            'item_id' => $this->faker->numberBetween(1, 10),
            'comment' => $this->faker->realText(rand(10, 255))
        ];
    }
}
