<?php
namespace App\Services;

use App\Trip;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class TripService
{
    public function getAllByUser(User $user): array
    {
        $trips = DB::table('trips')
            ->select([
                'trips.id',
                DB::raw("DATE_FORMAT(trips.date, '%m/%d/%Y') as date"),
                'trips.miles',
                'cars.id as car_id',
                'cars.make',
                'cars.model',
                'cars.year',
            ])
            ->join('cars', 'trips.car_id', '=', 'cars.id')
            ->addSelect([
                'total' => function (Builder $query): void {
                    $query->selectRaw('sum(car_trips.miles) as total')
                        ->from('trips as car_trips')
                        ->whereRaw('car_trips.car_id = cars.id');
                }
            ])
            ->where('trips.user_id', $user->id)
            ->groupBy('trips.id', 'trips.date', 'trips.miles', 'cars.id', 'cars.make', 'cars.model', 'cars.year')
            ->orderBy('trips.date', 'desc')
            ->get();

        return [
            'data' => $trips->map(fn($trip) => [
                'id' => $trip->id,
                'date' => $trip->date,
                'miles' => $trip->miles,
                'total' => (float) $trip->total,
                'car' => [
                    'id' => (float) $trip->car_id,
                    'make' => $trip->make,
                    'model' => $trip->model,
                    'year' => (int) $trip->year,
                ],

            ]),
        ];
    }

    public function createTrip(array $data, User $user): Trip
    {
        $trip = new Trip($data);
        $trip->user_id = $user->id;
        $trip->date = Carbon::parse($data['date']);
        $trip->save();

        return $trip;
    }
}
