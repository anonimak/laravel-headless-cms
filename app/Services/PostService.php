<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class PostService extends BaseService
{

    protected function getModelClass(): string
    {
        return Post::class;
    }

    // action publish and unpublish
    public function publish(Post $post): void
    {
        $post->status = 'published';
        $post->save();
    }

    public function unpublish(Post $post): void
    {
        $post->status = 'draft';
        $post->save();
    }

    // toggle publish status
    public function togglePublish(Post $post): void
    {
        if ($post->status === Post::STATUS_PUBLISHED) {
            $this->unpublish($post);
        } else {
            $this->publish($post);
        }
    }

    // attach categories to post
    public function attachCategories(Post $post, array $categoryIds): void
    {
        $post->categories()->sync($categoryIds);
    }

    public function getPaginatedPublished(?string $search = null, int $perPage = 10, array $with = []): LengthAwarePaginator
    {
        if ($search) {
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
            return $this->getModelClass()::with($with)
                ->where('status', 'published')
                ->latest()
                ->paginate($perPage);
        }
    }
}
