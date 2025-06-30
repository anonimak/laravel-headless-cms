<?php

namespace App\Services;

use App\Models\Category;

class CategoryService extends BaseService
{

    protected function getModelClass(): string
    {
        return Category::class;
    }
}
