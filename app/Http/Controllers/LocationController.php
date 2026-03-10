<?php

namespace App\Http\Controllers;

use App\Common\Constants\HttpStatus;
use App\Common\Traits\ApiResponseTrait;
use App\Models\City;
use App\Models\District;
use App\Models\Ward;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    use ApiResponseTrait;

    public function city(Request $request): JsonResponse
    {
        $query = City::query();

        if (!$request->boolean('all_status')) {
            $query->where('isactive', true);
        }

        $data = $query
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get([
                'id',
                'code',
                'name',
                'sort_order',
                'isactive',
            ])
            ->toArray();

        return $this->successResponse($data, 'Success', HttpStatus::OK);
    }

    public function district(Request $request): JsonResponse
    {
        $query = District::query();

        if (!$request->boolean('all_status')) {
            $query->where('isactive', true);
        }

        if ($request->filled('city_id')) {
            $query->where('city_id', $request->integer('city_id'));
        }

        $data = $query
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get([
                'id',
                'code',
                'name',
                'city_id',
                'sort_order',
                'isactive',
            ])
            ->toArray();

        return $this->successResponse($data, 'Success', HttpStatus::OK);
    }

    public function ward(Request $request): JsonResponse
    {
        $query = Ward::query();

        if (!$request->boolean('all_status')) {
            $query->where('isactive', true);
        }

        if ($request->filled('district_id')) {
            $query->where('district_id', $request->integer('district_id'));
        }

        if ($request->filled('city_id')) {
            $query->whereHas('district', function ($q) use ($request) {
                $q->where('city_id', $request->integer('city_id'));
            });
        }

        $data = $query
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get([
                'id',
                'code',
                'name',
                'district_id',
                'sort_order',
                'isactive',
            ])
            ->toArray();

        return $this->successResponse($data, 'Success', HttpStatus::OK);
    }
}
