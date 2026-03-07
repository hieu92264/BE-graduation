<?php

namespace App\Http\Services;

use App\Http\_base\BaseService;
use App\Http\Interfaces\CategoryServiceInterface;
use App\Models\Category;

class CategoryService extends BaseService implements CategoryServiceInterface
{
    protected function getModel(): string
    {
        return Category::class;
    }
}
