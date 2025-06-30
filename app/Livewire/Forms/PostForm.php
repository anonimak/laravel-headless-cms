<?php

namespace App\Livewire\Forms;

use App\Models\Post;
use App\Services\PostService;
use Livewire\Attributes\Validate;
use Livewire\Form;

class PostForm extends Form
{
    // form create, update, delete
    #[Validate('required|string|max:255')]
    public string $title = '';
    #[Validate('required|string')]
    public string $content = '';
    #[Validate('required|string|unique:posts,slug|max:255')]
    public string $slug = '';
    #[Validate('nullable|string')]
    public ?string $excerpt = null;
    #[Validate('nullable|string')]
    public ?string $image = null;
    #[Validate('required|string|in:draft,published')]
    public string $status = 'draft';

    public ?Post $post = null;

    public function setPost(?Post $post = null): void
    {
        $this->post = $post;

        if ($post->exists) {
            $this->fill($post->toArray());
        }
    }

    public function save(PostService $service): void
    {
        $validatedData = $this->validate();
        if ($this->post->exists) {
            $service->update($this->post, $validatedData);
        } else {
            $service->create($validatedData);
        }
    }

    public function publish(PostService $service): void
    {
        if ($this->post) {
            $service->publish($this->post);
        }
    }

    public function unpublish(PostService $service): void
    {
        if ($this->post) {
            $service->unpublish($this->post);
        }
    }

    // append category_post
    public function attachCategory(PostService $service, array $categoryIds): void
    {
        if ($this->post) {
            $service->attachCategories($this->post, $categoryIds);
        }
    }

    public function delete(PostService $service): void
    {
        if ($this->post) {
            $service->delete($this->post);
            $this->resetForm();
        }
    }

    public function resetForm(): void
    {
        $this->reset();
    }
}
