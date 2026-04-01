<?php

namespace App\Http\Controllers;

use App\Common\Constants\HttpStatus;
use App\Common\Traits\ApiResponseTrait;
use App\Models\PostType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PostTypeController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request): JsonResponse
    {
        $query = PostType::query();

        if (!$request->boolean('all_status')) {
            $query->where('isactive', 'Y');
        }

        $data = $query
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

        return $this->successResponse($data, 'Lấy danh sách loại bài đăng thành công', HttpStatus::OK);
    }

    public function options(Request $request): JsonResponse
    {
        $query = PostType::query();

        if (!$request->boolean('all_status')) {
            $query->where('isactive', 'Y');
        }

        $data = $query
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

        return $this->successResponse($data, 'Lấy chi tiết loại bài đăng thành công', HttpStatus::OK);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'isactive' => ['nullable'],
            'code' => ['required', 'string', 'max:50', 'unique:post_types,code'],
            'name' => ['required', 'string', 'max:255'],
            'priority' => ['nullable', 'integer', 'min:0'],
            'default_days' => ['nullable', 'integer', 'min:1'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'remark' => ['nullable', 'string'],
        ]);

        $data = [
            'isactive' => $validated['isactive'] ?? 'Y',
            'code' => $validated['code'],
            'name' => $validated['name'],
            'priority' => $validated['priority'] ?? 0,
            'default_days' => $validated['default_days'] ?? 30,
            'price' => $validated['price'] ?? 0,
            'remark' => $validated['remark'] ?? null,
            'user_name_created' => auth()->user()->username ?? auth()->user()->email ?? 'system',
            'user_name_updated' => auth()->user()->username ?? auth()->user()->email ?? 'system',
        ];

        $postType = PostType::create($data);

        return $this->successResponse(
            $postType->toArray(),
            'Tạo loại bài đăng thành công',
            HttpStatus::CREATED
        );
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $postType = PostType::withoutGlobalScopes()->findOrFail($id);

        $validated = $request->validate([
            'isactive' => ['nullable'],
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('post_types', 'code')->ignore($postType->id),
            ],
            'name' => ['required', 'string', 'max:255'],
            'priority' => ['nullable', 'integer', 'min:0'],
            'default_days' => ['nullable', 'integer', 'min:1'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'remark' => ['nullable', 'string'],
        ]);

        $postType->update([
            'isactive' => $validated['isactive'] ?? $postType->isactive,
            'code' => $validated['code'],
            'name' => $validated['name'],
            'priority' => $validated['priority'] ?? 0,
            'default_days' => $validated['default_days'] ?? 30,
            'price' => $validated['price'] ?? 0,
            'remark' => $validated['remark'] ?? null,
            'user_name_updated' => auth()->user()->username ?? auth()->user()->email ?? 'system',
        ]);

        return $this->successResponse(
            $postType->fresh()->toArray(),
            'Cập nhật loại bài đăng thành công',
            HttpStatus::OK
        );
    }

    public function destroy(int $id): JsonResponse
    {
        $postType = PostType::withoutGlobalScopes()->findOrFail($id);
        $postType->delete();

        return $this->successResponse([], 'Xóa loại bài đăng thành công', HttpStatus::OK);
    }
}
