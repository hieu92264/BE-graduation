<?php

namespace App\Http\Controllers;

use App\Common\Constants\HttpStatus;
use App\Http\Interfaces\CategoryServiceInterface;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function __construct(protected CategoryServiceInterface $service)
    {
    }

    public function getAll(): JsonResponse
    {
        $result = $this->service->getAll()->toArray();

        return $this->DataResponse(true, 'success', HttpStatus::OK, $result);
    }

    public function create(StoreCategoryRequest $request): JsonResponse
    {
        $data = $request->validated();
        $result = $this->service->create($data);

        return $this->DataResponse(true, 'success', HttpStatus::CREATED, $result->toArray());
    }

    public function update(UpdateCategoryRequest $request, int $id): JsonResponse
    {
        $data = $request->validated();
        $result = $this->service->update($id, $data);

        return $this->DataResponse(true, 'success', HttpStatus::OK, $result->toArray());
    }

    public function delete(int $id): JsonResponse
    {
        $this->service->delete($id);

        return $this->DataResponse(true, 'success', HttpStatus::OK, []);
    }
}
