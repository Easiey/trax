<?php

namespace Database\Factories;

use App\Car;
use App\User;
use Illuminate\Database\Eloquent\Factories\Factory;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Trip>
 */
class TripFactory extends Factory
{
    public function definition()
    {
        return [
            'date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'miles' => $this->faker->randomFloat(2, 0, 100),
            'car_id' => Car::factory()->create(),
            'user_id' => User::factory()->create(),
        ];
    }
}
