<?php

namespace App\Http\_base;

use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

abstract class BaseService
{
    protected Model $model;

    public function __construct()
    {
        $this->model = app($this->getModel());
    }

    abstract protected function getModel(): string;

    public function getAll(): Collection
    {
        try {
            return $this->model->all();
        } catch (Exception $e) {
            Log::error('BaseService GetAll Error: ' . $e->getMessage());
            throw new Exception($e->getMessage());
        }
    }

    public function create(array $attributes): Model
    {
        $attributes['user_name_created'] = Auth::user()->username ?? null;

        try {
            return $this->model->create($attributes);
        } catch (Exception $e) {
            Log::error('BaseService Create Error: ' . $e->getMessage());
            throw new Exception('messages.common.record_create_failed');
        }
    }

    public function update(int $id, array $attributes): Model
    {
        $result = $this->model->findOrFail($id);

        try {
            $attributes['user_name_updated'] = Auth::user()->username ?? null;
            $result->update($attributes);

            return $result;
        } catch (Exception $e) {
            Log::error("BaseService Update Error ID {$id}: " . $e->getMessage());
            throw new Exception('messages.common.record_update_failed');
        }
    }

    public function delete(int $id): bool
    {
        $result = $this->model->findOrFail($id);

        try {
            return (bool) $result->delete();
        } catch (Exception $e) {
            Log::error("BaseService Delete Error ID {$id}: " . $e->getMessage());
            throw new Exception('messages.common.record_delete_failed');
        }
    }

    public function getDataPagination(
        int $perPage = 15,
        int $page = 1,
        array $filters = [],
        array $sort = []
    ): LengthAwarePaginator {
        try {
            $query = $this->model->newQuery();

            if (! empty($filters)) {
                foreach ($filters as $column => $value) {
                    if ($value !== null && $value !== '') {
                        $query->where($column, 'LIKE', '%' . $value . '%');
                    }
                }
            }

            if (! empty($sort) && isset($sort['column'])) {
                $direction = (strtolower($sort['direction'] ?? 'asc') === 'desc') ? 'desc' : 'asc';
                $query->orderBy($sort['column'], $direction);
            } else {
                $query->latest();
            }

            return $query->paginate($perPage, ['*'], 'page', $page);
        } catch (Exception $e) {
            Log::error('BaseService Pagination Error: ' . $e->getMessage());
            throw new Exception('messages.common.pagination_load_failed');
        }
    }
}
