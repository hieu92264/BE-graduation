<?php

namespace App\Http\_base;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface BaseServiceInterface
{
    public function getAll(): Collection;

    public function create(array $attributes): Model;

    public function update(int $id, array $attributes): bool|Model;

    public function delete(int $id): bool;

    public function getDataPagination(int $perPage = 15, int $page = 1, array $filters = [], array $sort = []): LengthAwarePaginator;
}
