<?php

namespace App\Http\Controllers;

use App\Http\Requests\TripRequest;
use App\Http\Resources\TripResource;
use App\Services\TripService;
use App\Trip;
use Illuminate\Support\Facades\Auth;

class TripController extends Controller
{
    private TripService $tripService;

    public function __construct(TripService $tripService)
    {
        $this->tripService = $tripService;
    }

    public function index(): array
    {
        return $this->tripService->getAllByUser(Auth::user());
    }

    public function store(TripRequest $request): TripResource
    {
        $this->authorize('create', [Trip::class, $request->input('car_id')]);

        $tripData = $request->validated();
        $user = Auth::user();
        $trip = $this->tripService->createTrip($tripData, $user);

        return new TripResource($trip);
    }
}

