<?php

namespace App\Http\Controllers;

use App\Common\Traits\ApiResponseTrait;
use App\Models\PostType;
use Illuminate\Http\JsonResponse;

class PostTypeController extends Controller
{
    use ApiResponseTrait;

    public function index(): JsonResponse
    {
        $data = PostType::query()
            ->orderByDesc('priority')
            ->orderBy('name')
            ->get([
                'id',
                'isactive',
                'code',
                'name',
                'priority',
                'default_days',
                'price',
                'remark',
                'created_at',
                'updated_at',
            ])
            ->toArray();

        return $this->successResponse($data);
    }

    public function options(): JsonResponse
    {
        $data = PostType::query()
            ->orderByDesc('priority')
            ->orderBy('name')
            ->get([
                'id',
                'code',
                'name',
                'priority',
                'default_days',
                'price',
            ])
            ->toArray();

        return $this->successResponse($data);
    }
}
