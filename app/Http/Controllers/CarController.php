<?php

namespace App\Http\Controllers;

use App\Http\Requests\CarRequest;
use App\Http\Resources\CarResource;
use App\Car;
use App\Services\CarService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

class CarController extends Controller
{
    private CarService $carService;

    public function __construct(CarService $carService)
    {
        $this->carService = $carService;
    }

    public function index(): AnonymousResourceCollection
    {
        $cars = $this->carService->getAllCars();

        return CarResource::collection($cars);
    }

    public function store(CarRequest $request): CarResource
    {
        $carData = $request->validated();
        $user = Auth::user();

        $car = $this->carService->createCar($carData, $user);

        return new CarResource($car);
    }

    public function show(Car $car): CarResource
    {
        return new CarResource($car);
    }

    public function destroy(Car $car): JsonResponse
    {
        $this->carService->deleteCar($car);

        return response()->json(null, 204);
    }
}

