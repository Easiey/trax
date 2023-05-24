<?php

namespace Tests\Feature;

use App\Car;
use App\Trip;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class TripControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testReturnsUserTrips(): void
    {
        $user = User::factory()->create();

        Car::factory()
            ->has(Trip::factory(2)->for($user))
            ->for($user)
            ->create();

        $response = $this->actingAs($user, 'api')
            ->withSession(['banned' => false])
            ->get('/api/trips');

        $response->assertStatus(200);

        $response->assertJson(fn(AssertableJson $json) => $json->has('data', 2, fn(
            AssertableJson $json) => $json->whereAllType(
            [
                'id' => 'integer',
                'date' => 'string',
                'miles' => 'double|integer',
                'total' => 'double|integer',
                'car.id' => 'integer',
                'car.make' => 'string',
                'car.model' => 'string',
                'car.year' => 'integer',
            ]
        )));
    }


    public function testCreatesTrip(): void
    {
        $user = User::factory()->create();

        $car = Car::factory()
            ->for($user)
            ->create();

        $response = $this->actingAs($user, 'api')
            ->withSession(['banned' => false])
            ->postJson('/api/trips',
                [
                    'date' => '02/12/2022',
                    'car_id' => $car->id,
                    'miles' => 1.5,
                ]
            );
        $response->assertStatus(201);
    }


    public function testFailsToCreateForUnauthorized(): void
    {
        $user = User::factory()->create();
        $car = Car::factory()
            ->for($user)
            ->create();

        $response = $this
            ->postJson('/api/trips',
                [
                    'date' => '02/12/2022',
                    'car_id' => $car->id,
                    'miles' => 1.5,
                ]
            );

        $response->assertUnauthorized();
    }

    public function testFailsToCreateForADifferentUserCar(): void
    {
        $userMakingRequest = User::factory()->create();

        $carOwner = User::factory()->create();
        $car = Car::factory()
            ->for($carOwner)
            ->create();

        $response = $this->actingAs($userMakingRequest, 'api')
            ->withSession(['banned' => false])
            ->postJson('/api/trips',
                [
                    'date' => '02/12/2022',
                    'car_id' => $car->id,
                    'miles' => 1.5,
                ]
            );

        $response->assertNotFound();
    }
}
