<?php

namespace App\Http\Controllers;

use App\Common\Constants\HttpStatus;
use App\Http\Interfaces\PermissionServiceInterface;
use App\Http\Requests\StorePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;
use Illuminate\Http\JsonResponse;

class PermissionController extends Controller
{
    public function __construct(protected PermissionServiceInterface $service)
    {

    }

    public function getAll(): JsonResponse
    {
        $result = $this->service->getAll()->toArray();

        return $this->DataResponse(true, 'Lấy danh sách quyền thành công', HttpStatus::OK, $result);
    }

    public function create(StorePermissionRequest $request): JsonResponse
    {
        $data = $request->validated();
        $result = $this->service->create($data);
        return $this->DataResponse(true, 'Tạo quyền thành công', HttpStatus::CREATED, $result->toArray());
    }

    public function update(UpdatePermissionRequest $request, int $id): JsonResponse
    {
        $data = $request->validated();
        $result = $this->service->update($id, $data);
        return $this->DataResponse(true, 'Cập nhật quyền thành công', HttpStatus::OK, $result->toArray());
    }

    public function delete(int $id): JsonResponse
    {
        $result = $this->service->delete($id);
        return $this->DataResponse(true, 'Xóa quyền thành công', HttpStatus::OK, []);
    }

    public function getPermissionOptions(): JsonResponse
    {
        $data = $this->service->getPermissionOptions();
        return $this->successResponse($data, 'Lấy chi tiết quyền thành công', HttpStatus::OK);
    }
}
