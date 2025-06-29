<?php

use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Title};
use function Livewire\Volt\{title, layout};
use App\Models\Category;
use App\Livewire\Forms\CategoryForm;
use App\Services\CategoryService;

layout('layouts.app');
title('Categories');
new class extends Component {
    public CategoryForm $form;
    public bool $isLoading = false;
    // public ?Category $category = null;

    //

    public function onConfirmDelete(CategoryService $service, Category $category): void
    {
        $this->isLoading = true;
        $this->form->setCategory($category);
        try {
            if (!$category) {
                throw new \Exception('Category not found.');
            }
            $this->form->delete($service);
            $this->dispatch('category-deleted');
            $this->dispatch('show-flash', [
                'message' => 'Category successfully deleted.',
                'type' => 'success',
            ]);
            Flux::modal('delete-confirmation-modal')->close();
            $this->isLoading = false;
        } catch (\Exception $e) {
            $this->dispatch('show-flash', [
                'message' => 'Category not found.',
                'type' => 'error',
            ]);
            $this->isLoading = false;
            return;
        }
    }
};

?>


<x-slot name="breadcrumb">
    <flux:breadcrumbs>
        <flux:breadcrumbs.item :href="route('dashboard')" icon="home" label="Dashboard" wire:navigate />
        <flux:breadcrumbs.item>Category</flux:breadcrumbs.item>
    </flux:breadcrumbs> {{-- Anda sepertinya salah ketik, seharusnya </flux:breadcrumbs> --}}
</x-slot>

<flux:main container>

    <div class="flex justify-between items-center mb-6">
        <flux:heading size="xl" level="1">Category</flux:heading>
        <flux:button class="cursor-pointer" icon="plus" size="sm" variant="primary" @click="$dispatch('open-form')">
            Create New
        </flux:button>
    </div>
    <flux:text class="mt-2 mb-6 text-base">The description is a great place for tone setting, high level
        information, and editing guidelines that are specific to a
        collection.</flux:text>
    <flux:separator variant="subtle" />

    {{-- Memanggil komponen Livewire lain bekerja dengan cara yang sama persis --}}
    <livewire:category.list />
    <livewire:category.form />
    <livewire:common.delete-confirm-modal :$isLoading @btn-delete-click="onConfirmDelete(id)" />
</flux:main>
