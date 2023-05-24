<?php

namespace Tests\Unit;

use App\Services\CarService;
use App\Car;
use App\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CarServiceTest extends TestCase
{
    use RefreshDatabase;

    private CarService $carService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->carService = new CarService();
    }

    public function testGetAllCarsReturnsCollection()
    {
        $cars = $this->carService->getAllCars();

        $this->assertInstanceOf(Collection::class, $cars);
    }

    public function testCreateCarSavesCarToDatabase()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $carData = [
            'make' => 'Ford',
            'model' => 'Mustang',
            'year' => 2022,
        ];

        $car = $this->carService->createCar($carData, $user);

        $this->assertDatabaseHas('cars', $carData);
        $this->assertEquals($user->id, $car->user_id);
    }

    public function testDeleteCarDeletesCarFromDatabase()
    {
        $this->withoutExceptionHandling();

        $car = Car::factory()->create();

        $this->carService->deleteCar($car);

        $this->assertDatabaseMissing('cars', ['id' => $car->id]);
    }
}

