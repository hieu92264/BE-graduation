<?php

namespace App\Http\Controllers;

use App\Common\Constants\HttpStatus;
use App\Http\Interfaces\CategoryServiceInterface;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function __construct(protected CategoryServiceInterface $service) {}

    public function getAll(): JsonResponse
    {
        $result = $this->service->getAll()->toArray();

        return $this->DataResponse(true, 'Lấy danh sách danh mục thành công', HttpStatus::OK, $result);
    }

    public function create(StoreCategoryRequest $request): JsonResponse
    {
        $data = $request->validated();
        $result = $this->service->create($data);

        return $this->DataResponse(true, 'Tạo danh mục thành công', HttpStatus::CREATED, $result->toArray());
    }

    public function update(UpdateCategoryRequest $request, int $id): JsonResponse
    {
        $data = $request->validated();
        $result = $this->service->update($id, $data);

        return $this->DataResponse(true, 'Cập nhật danh mục thành công', HttpStatus::OK, $result->toArray());
    }

    public function delete(int $id): JsonResponse
    {
        $this->service->delete($id);

        return $this->DataResponse(true, 'Xóa danh mục thành công', HttpStatus::OK, []);
    }

    public function options(): JsonResponse
    {
        $data = Category::query()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get([
                'id',
                'code',
                'name',
                'slug',
                'sort_order',
                'remark',
                'isactive',
                'created_at',
                'updated_at',
            ])
            ->toArray();

        return $this->DataResponse(true, 'Lấy chi tiết danh mục thành công', HttpStatus::OK, $data);
    }
}
