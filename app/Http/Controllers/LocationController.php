<?php

namespace App\Http\Controllers;

use App\Common\Traits\ApiResponseTrait;
use App\Models\City;
use App\Models\District;
use App\Models\Ward;
use Illuminate\Http\JsonResponse;

class LocationController extends Controller
{
    use ApiResponseTrait;

    public function city(): JsonResponse
    {
        $data = City::all()->toArray();
        return $this->successResponse($data);
    }

    public function district(): JsonResponse
    {
        $data = District::all()->toArray();
        return $this->successResponse($data);
    }

    public function ward(): JsonResponse
    {
        $data = Ward::all()->toArray();
        return $this->successResponse($data);
    }
}
