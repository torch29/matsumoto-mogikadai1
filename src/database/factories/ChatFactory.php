<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ChatFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    //ChatTest用
    public function definition()
    {
        return [
            'purchase_id' => rand(1, 3),
            'sender_id' => rand(1, 2),
            'message' => $this->faker->realText(rand(10, 400)),
        ];
    }
}
