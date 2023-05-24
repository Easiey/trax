<?php

namespace Tests\Feature;

use App\Car;
use App\Trip;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class CarControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testReturnsUserCars(): void
    {
        $user = User::factory()->create();

        Car::factory()
            ->count(3)
            ->for($user)
            ->create();

        $response = $this->actingAs($user, 'api')
            ->withSession(['banned' => false])
            ->get('/api/cars');

        $response->assertStatus(200);

        $response->assertJson(fn(AssertableJson $json) => $json->has('data', 3, fn(
            AssertableJson $json) => $json->whereAllType(
            [
                'id' => 'integer',
                'make' => 'string',
                'model' => 'string',
                'year' => 'integer',
                'trip_count' => 'integer',
                'trip_miles' => 'integer',
            ]
        )));
    }

    public function testReturnsCar(): void
    {
        $user = User::factory()->create();

        $car = Car::factory()
            ->has(Trip::factory(3)->for($user))
            ->for($user)
            ->create();

        $response = $this->actingAs($user, 'api')
            ->withSession(['banned' => false])
            ->get('/api/cars/' . $car->id);

        $response->assertStatus(200);
        $response->assertJson(fn(
            AssertableJson $json) => $json->whereAll(
            [
                'data.id' => $car->id,
                'data.make' => $car->make,
                'data.model' => $car->model,
                'data.year' => $car->year,
                'data.trip_count' => $car->tripCount(),
                'data.trip_miles' => $car->tripMiles(),
            ]
        ));
    }

    public function testCreatesCar(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'api')
            ->withSession(['banned' => false])
            ->postJson('/api/cars',
                [
                    'year' => 2010,
                    'make' => 'Random word',
                    'model' => 'Toyota',
                ]
            );

        $response->assertStatus(201);
        $response->assertJson(fn(
            AssertableJson $json) => $json->whereAll(
            [
                'data.year' => 2010,
                'data.make' => 'Random word',
                'data.model' => 'Toyota',
            ]
        ));
    }

    public function testDeletesCar(): void
    {
        $user = User::factory()->create();

        $car = Car::factory()
            ->for($user)
            ->create();

        $response = $this->actingAs($user, 'api')
            ->withSession(['banned' => false])
            ->delete('/api/cars/' . $car->id);

        $response->assertStatus(204);
    }

    public function testFailsToCreateForUnauthorized(): void
    {
        $response = $this
            ->postJson('/api/cars',
                [
                    'year' => 2010,
                    'make' => 'Random word',
                    'model' => 'Toyota',
                ]
            );

        $response->assertUnauthorized();
    }

    public function testFailsToDeleteForUnauthorized(): void
    {
        $user = User::factory()->create();

        $car = Car::factory()
            ->for($user)
            ->create();

        $response = $this->delete('/api/cars/' . $car->id);

        $response->assertStatus(302);
    }

    public function testFailsToReturnDifferentUserCar (): void
    {
        $carOwner = User::factory()->create();

        $userMakingRequest = User::factory()->create();

        $car = Car::factory()
            ->has(Trip::factory(3)->for($carOwner))
            ->for($carOwner)
            ->create();

        $response = $this->actingAs($userMakingRequest, 'api')
            ->withSession(['banned' => false])
            ->get('/api/cars/' . $car->id);

        $response->assertNotFound();
    }
    public function testFailsToDeleteDifferentUserCar(): void
    {
        $carOwner = User::factory()->create();

        $userMakingRequest = User::factory()->create();

        $car = Car::factory()
            ->for($carOwner)
            ->create();

        $response = $this->actingAs($userMakingRequest, 'api')
            ->withSession(['banned' => false])
            ->delete('/api/cars/' . $car->id);

        $response->assertNotFound();
    }

}
