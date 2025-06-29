<?php

namespace App\Livewire\Forms;

use App\Models\Category;
use App\Services\CategoryService;
use Livewire\Attributes\Validate;
use Livewire\Form;

class CategoryForm extends Form
{
    // form create, update, delete
    #[Validate('required|string|max:255')]
    public string $name = '';
    #[Validate('required|string|max:255')]
    public string $slug = '';
    #[Validate('nullable|string|max:255')]
    public ?string $description = null;

    public ?Category $category = null;

    public function setCategory(?Category $category = null): void
    {
        $this->category = $category;

        if ($category->exists) {
            $this->fill($category->toArray());
        }
    }

    public function save(CategoryService $service): void
    {
        $validatedData = $this->validate();
        if ($this->category->exists) {
            $service->update($this->category, $validatedData);
        } else {
            $service->create($validatedData);
        }
    }

    public function delete(CategoryService $service): void
    {
        if ($this->category) {
            $service->delete($this->category);
            $this->resetForm();
        }
    }

    public function resetForm(): void
    {
        $this->reset();
    }
}
