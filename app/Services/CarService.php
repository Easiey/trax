<?php
namespace App\Services;

use App\Car;
use App\User;
use Illuminate\Database\Eloquent\Collection;

class CarService
{
    public function getAllCars(): Collection
    {
        return Car::withCount('trips')
            ->withSum('trips', 'miles')
            ->get();
    }

    public function createCar(array $data, User $user): Car
    {
        $car = new Car($data);
        $car->user_id = $user->id;
        $car->save();

        return $car;
    }

    public function deleteCar(Car $car): void
    {
        $car->delete();
    }
}
