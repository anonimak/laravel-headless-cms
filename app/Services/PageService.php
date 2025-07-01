<?php

namespace App\Services;

use App\Models\Page;

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
}
