<?php

namespace App\Services\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

trait ManagesData
{

    public function getPaginated(?string $search = null, int $perPage = 10, array $with = []): LengthAwarePaginator
    {
        return $this->getModelClass()::search($search, function (Builder $query) use ($with) {
            if (!empty($with)) {
                return $query->with($with);
            }
            return $query;
        })

            ->latest()
            ->paginate($perPage);
    }

    // get by id
    public function getById(int $id, array $with = []): ?Model
    {
        return $this->getModelClass()::with($with)->find($id);
    }

    public function create(array $data): Model
    {
        return $this->getModelClass()::create($data);
    }

    public function update(Model $model, array $data): bool
    {
        return $model->update($data);
    }

    public function delete(Model $model): ?bool
    {
        return $model->delete();
    }
}
