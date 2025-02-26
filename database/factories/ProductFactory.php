<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id'=>$this->faker->unique()->ean8,
            'name'=>$this->faker->unique()->sentence(8),
            'price'=>$this->faker->numberBetween(1000, 1000000),
            'user_id'=>User::factory(),
        ];
    }
}
