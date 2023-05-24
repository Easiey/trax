<?php

namespace Tests\Unit;

use App\Car;
use App\Services\TripService;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TripServiceTest extends TestCase
{
    use RefreshDatabase;

    private TripService $tripService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tripService = new TripService();
    }

    public function testCreateTripSavesTripToDatabase()
    {
        $user = User::factory()->create();
        $car = Car::factory()->create(['user_id' => $user->id]);

        $tripData = [
            'date' => Carbon::now()->format('Y-m-d'),
            'miles' => 10.5,
            'car_id' => $car->id
        ];

        $trip = $this->tripService->createTrip($tripData, $user);

        $this->assertDatabaseHas('trips', [
            'id' => $trip->id,
            'date' => $tripData['date'],
            'miles' => $tripData['miles'],
            'user_id' => $user->id,
            'car_id' => $car->id
        ]);
    }
}

