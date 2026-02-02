<?php

namespace App\Http\Controllers;

use App\Common\Constants\HttpStatus;
use App\Http\Interfaces\EmployeeServiceInterface;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function __construct(protected EmployeeServiceInterface $employeeService)
    {
    }

    public function getAll(): JsonResponse
    {
        $result = $this->employeeService->getAll()->toArray();

        return $this->DataResponse(true, 'success', HttpStatus::OK, $result);
    }

    public function create(StoreEmployeeRequest $request): JsonResponse
    {
        $attributes = $request->validated();
        $result = $this->employeeService->create($attributes)->toArray();

        return $this->DataResponse(true, 'success', HttpStatus::CREATED, $result);
    }

    public function update(UpdateEmployeeRequest $request, string $id): JsonResponse
    {
        $attributes = $request->validated();
        $result = $this->employeeService->update((int)$id, $attributes)->toArray();

        return $this->DataResponse(true, 'success', HttpStatus::CREATED, $result);
    }

    public function delete(string $id)
    {
        $result = $this->employeeService->delete((int)$id);
        if ($result) {
            return $this->DataResponse(true, 'Xóa nhân viên thành công', HttpStatus::OK, ['employeeId' => $id]);
        }

        return $this->DataResponse(false, 'Xóa nhân viên thất bại', HttpStatus::BAD_REQUEST, null);
    }

    public function getUserOptions(Request $request): JsonResponse
    {
        $userId = $request->query('userId');
        $result = $this->employeeService->getUserOptions($userId);

        return $this->DataResponse(true, 'success', HttpStatus::OK, $result);
    }
}
