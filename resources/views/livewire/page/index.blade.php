<?php

use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Title, On};
use function Livewire\Volt\{title, layout};
use App\Models\Page;
use App\Livewire\Forms\PageForm;
use App\Services\PageService;

layout('layouts.app');
title('Page');
new class extends Component {
    public PageForm $form;
    public bool $isLoading = false;
    // public ?Page $page = null;

    //

    public function onConfirmDelete(PageService $service, Page $page): void
    {
        $this->isLoading = true;
        $this->form->setPage($page);
        try {
            if (!$page) {
                throw new \Exception('Page not found.');
            }
            $this->form->delete($service);
            $this->dispatch('page-deleted');
            $this->dispatch('show-flash', [
                'message' => 'Page successfully deleted.',
                'type' => 'success',
            ]);
            Flux::modal('dialog-confirm-modal')->close();
            $this->isLoading = false;
        } catch (\Exception $e) {
            $this->dispatch('show-flash', [
                'message' => 'Page not found.',
                'type' => 'error',
            ]);
            $this->isLoading = false;
            return;
        }
    }

    public function onPageStatusToggled(PageService $service, Page $page): void
    {
        $this->isLoading = true;
        $this->form->setPage($page);
        try {
            $this->form->togglePublish($service);
            $this->dispatch('page-upserted');
            $this->dispatch('show-flash', [
                'message' => 'Page status updated successfully.',
                'type' => 'success',
            ]);
            Flux::modal('dialog-confirm-modal')->close();
            $this->isLoading = false;
        } catch (\Exception $e) {
            $this->dispatch('show-flash', [
                'message' => 'Failed to update page status: ' . $e->getMessage(),
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
        <flux:breadcrumbs.item>Page</flux:breadcrumbs.item>
    </flux:breadcrumbs>
</x-slot>

<flux:main container>

    <div class="flex justify-between items-center mb-6">
        <flux:heading size="xl" level="1">Page</flux:heading>
        <flux:button class="cursor-pointer" icon="plus" size="sm" variant="primary"
            wire:click="$dispatch('open-form')">
            Create New
        </flux:button>
    </div>
    <flux:text class="mt-2 mb-6 text-base">The description is a great place for tone setting, high level
        information, and editing guidelines that are specific to a
        collection.</flux:text>
    <flux:separator variant="subtle" />


    <livewire:page.list />
    <livewire:page.form />
    <livewire:common.dialog-confirm-modal :$isLoading @btn-delete-click="onConfirmDelete(id)"
        @page-status-toggled="onPageStatusToggled(id)" />
</flux:main>
