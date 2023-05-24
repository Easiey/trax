<?php

namespace App\Policies;

use App\Car;
use App\Trip;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TripPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Trip $trip): bool
    {
        return $user->id === $trip->user_id;
    }

    public function create(User $user, $carId)
    {
        $car = Car::findOrFail($carId);
        return $user->id === $car->user_id;
    }

    public function update(User $user, Trip $trip): bool
    {
        return $user->id === $trip->user_id;
    }

    public function delete(User $user, Trip $trip): bool
    {
        return $user->id === $trip->user_id;
    }
}
