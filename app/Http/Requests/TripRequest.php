<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TripRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'date' => 'required|date',
            'car_id' => 'required|integer',
            'miles' => 'required|numeric|gt:0'
        ];
    }
}
