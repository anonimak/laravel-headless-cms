<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CategoryService extends BaseService
{

    protected function getModelClass(): string
    {
        return Category::class;
    }
}
