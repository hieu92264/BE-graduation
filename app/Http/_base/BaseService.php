<?php

namespace App\Http\_base;

use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
/// Lỗi sai nghiêm trọng, nên dùng thực tiếp Eloquent, bản thân Eloquent nó cũng đã chính là 1 Respository rồi, e ko nên tạo ra lớp abstraction làm gì
/// khiến ứng dụng phức tạp ra, nếu cần xử lý các sự kiện liên quan có thể bắn event để các handler nó sẽ xử lý nó
/// càng abstraction, càng gây phức tạp, thiếu hiệu quả , chỉ dùng abstraction khi thực sự cần thiết
/// nên đọc và tìm hiểu nguyên lý Composition Over Inheritance
/// 
abstract class BaseService
{
    /**
     * @var Model
     */
    protected Model $model;

    public function __construct()
    {
        $this->model = app($this->getModel());
    }

    /**
     * @return string
     */
    abstract protected function getModel(): string;

    /**
     * @return Collection
     * @throws Exception
     */
    public function getAll(): Collection
    {
        try {
            return $this->model->all();
        } catch (Exception $e) {
            Log::error("BaseService GetAll Error: " . $e->getMessage());
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @param array $attributes
     * @return Model
     * @throws Exception
     */
    public function create(array $attributes): Model
    {
        $attributes['user_name_created'] = Auth::user()->username ?? null;
        try {
            return $this->model->create($attributes);
        } catch (Exception $e) {
            Log::error("BaseService Create Error: " . $e->getMessage());
            throw new Exception("Tạo bản ghi mới thất bại: " . $e->getMessage());
        }
    }

    /**
     * @param int $id
     * @param array $attributes
     * @return Model
     * @throws Exception
     */
    public function update(int $id, array $attributes): Model
    {
        $result = $this->model->findOrFail($id);

        try {
            $attributes['user_name_updated'] = Auth::user()->username ?? null;
            $result->update($attributes);
            return $result;
        } catch (Exception $e) {
            Log::error("BaseService Update Error ID {$id}: " . $e->getMessage());
            throw new Exception("Cập nhật dữ liệu thất bại.");
        }
    }

    /**
     * @param int $id
     * @return bool|mixed
     * @throws Exception
     */
    public function delete(int $id): bool
    {
        $result = $this->model->findOrFail($id);
        try {
            return (bool)$result->delete();
        } catch (Exception $e) {
            Log::error("BaseService Delete Error ID {$id}: " . $e->getMessage());
            throw new Exception("Xóa dữ liệu thất bại.");
        }
    }

    /**
     * @param int $id
     * @return bool
     */
    public function softDelete(int $id): bool
    {
        // hàm này có nghĩ nghĩa gì đâu @@@, nếu model bật sẵn soft delete thì nó đã softDelete luôn r, chỉ cần delete và forceDelete
        return $this->delete($id);
    }

    /**
     * @param int $perPage Số bản ghi trên mỗi trang
     * @param int $page Trang cụ thể muốn lấy
     * @param array $filters Mảng lọc dạng ['column' => 'value']
     * @param array $sort Mảng sắp xếp ['column' => 'id', 'direction' => 'desc']
     * @return LengthAwarePaginator
     * @throws Exception
     */
    public function getDataPagination(
        int   $perPage = 15,
        int   $page = 1,
        array $filters = [],
        array $sort = []
    ): LengthAwarePaginator
    {
        try {
            $query = $this->model->newQuery();

            if (!empty($filters)) {
                foreach ($filters as $column => $value) {
                    if ($value !== null && $value !== '') {
                        $query->where($column, 'LIKE', '%' . $value . '%');
                    }
                }
            }

            if (!empty($sort) && isset($sort['column'])) {
                $direction = (strtolower($sort['direction'] ?? 'asc') === 'desc') ? 'desc' : 'asc';
                $query->orderBy($sort['column'], $direction);
            } else {
                $query->latest();
            }

            return $query->paginate($perPage, ['*'], 'page', $page);

        } catch (Exception $e) {
            Log::error("BaseService Pagination Error: " . $e->getMessage());
            throw new Exception("Lỗi khi tải dữ liệu phân trang.");
        }
    }
}
