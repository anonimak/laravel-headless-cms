<?php

namespace App\Services;

use App\Models\Post;

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
}
