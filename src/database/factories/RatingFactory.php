<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Purchase;
use App\Models\User;

class RatingFactory extends Factory
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
            'purchase_id' => Purchase::factory(),
            'reviewer_id' => User::factory(),
            'reviewee_id' => User::factory(),
            'score' => rand(1, 5),
        ];
    }
}
