<?php

namespace App\Livewire\Forms;

use App\Models\Page;
use App\Services\PageService;
use Livewire\Attributes\Validate;
use Livewire\Form;

class PageForm extends Form
{
    // form create, update, delete
    #[Validate('required|string|max:255')]
    public string $title = '';
    #[Validate('required|string')]
    public string $body = '';
    #[Validate('required|string|unique:pages,slug|max:255')]
    public string $slug = '';
    #[Validate('required|string|in:draft,published')]
    public string $status = 'draft';

    public ?Page $page = null;

    public function setPage(?Page $page = null): void
    {
        $this->page = $page;

        if ($page->exists) {
            $this->fill($page->toArray());
        }
    }

    public function save(PageService $service): void
    {
        $validatedData = $this->validate();
        if ($this->page->exists) {
            $service->update($this->page, $validatedData);
        } else {
            $service->create($validatedData);
        }
    }
    // toggle publish status
    public function togglePublish(PageService $service): void
    {
        if ($this->page) {
            $service->togglePublish($this->page);
        }
    }

    public function delete(PageService $service): void
    {
        if ($this->page) {
            $service->delete($this->page);
            $this->resetForm();
        }
    }

    public function resetForm(): void
    {
        $this->reset();
    }
}
