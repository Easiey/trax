<?php

namespace Database\Factories;

use App\User;
use Illuminate\Database\Eloquent\Factories\Factory;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Car>
 */
class CarFactory extends Factory
{
    public function definition()
    {
        return [
            'make' => $this->faker->company,
            'model' => $this->faker->word,
            'year' => $this->faker->numberBetween(2000, 2023),
            'user_id' => function () {
                return User::factory()->create()->id;
            },
        ];
    }
}
