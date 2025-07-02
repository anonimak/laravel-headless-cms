<?php

namespace App\Services;

use App\Models\Page;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class PageService extends BaseService
{

    protected function getModelClass(): string
    {
        return Page::class;
    }

    // action publish and unpublish
    public function publish(Page $page): void
    {
        $page->status = 'published';
        $page->save();
    }

    public function unpublish(Page $page): void
    {
        $page->status = 'draft';
        $page->save();
    }

    // toggle publish status
    public function togglePublish(Page $page): void
    {
        if ($page->status === Page::STATUS_PUBLISHED) {
            $this->unpublish($page);
        } else {
            $this->publish($page);
        }
    }

    public function getPaginatedPublished(?string $search = null, int $perPage = 10, array $with = []): LengthAwarePaginator
    {
        if ($search) {
            // Use Scout search for published pages
            return $this->getModelClass()::search($search, function (Builder $query) use ($with) {
                $query->where('status', 'published');
                if (!empty($with)) {
                    return $query->with($with);
                }
                return $query;
            })
                ->latest()
                ->paginate($perPage);
        } else {
            // Regular query for published pages
            return $this->getModelClass()::with($with)
                ->where('status', 'published')
                ->latest()
                ->paginate($perPage);
        }
    }
}
