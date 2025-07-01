<?php

use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Title, On};
use function Livewire\Volt\{title, layout};
use App\Models\Post;
use App\Livewire\Forms\PostForm;
use App\Services\PostService;

layout('layouts.app');
title('Post');
new class extends Component {
    public PostForm $form;
    public bool $isLoading = false;
    // public ?Post $post = null;

    //

    public function onConfirmDelete(PostService $service, Post $post): void
    {
        $this->isLoading = true;
        $this->form->setPost($post);
        try {
            if (!$post) {
                throw new \Exception('Post not found.');
            }
            $this->form->delete($service);
            $this->dispatch('post-deleted');
            $this->dispatch('show-flash', [
                'message' => 'Post successfully deleted.',
                'type' => 'success',
            ]);
            Flux::modal('dialog-confirm-modal')->close();
            $this->isLoading = false;
        } catch (\Exception $e) {
            $this->dispatch('show-flash', [
                'message' => 'Post not found.',
                'type' => 'error',
            ]);
            $this->isLoading = false;
            return;
        }
    }

    public function onPostStatusToggled(PostService $service, Post $post): void
    {
        $this->isLoading = true;
        $this->form->setPost($post);
        try {
            $this->form->togglePublish($service);
            $this->dispatch('post-upserted');
            $this->dispatch('show-flash', [
                'message' => 'Post status updated successfully.',
                'type' => 'success',
            ]);
            Flux::modal('dialog-confirm-modal')->close();
            $this->isLoading = false;
        } catch (\Exception $e) {
            $this->dispatch('show-flash', [
                'message' => 'Failed to update post status: ' . $e->getMessage(),
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
        <flux:breadcrumbs.item>Post</flux:breadcrumbs.item>
    </flux:breadcrumbs>
</x-slot>

<flux:main container>

    <div class="flex justify-between items-center mb-6">
        <flux:heading size="xl" level="1">Post</flux:heading>
        <flux:button class="cursor-pointer" icon="plus" size="sm" variant="primary"
            wire:click="$dispatch('open-form')">
            Create New
        </flux:button>
    </div>
    <flux:text class="mt-2 mb-6 text-base">The description is a great place for tone setting, high level
        information, and editing guidelines that are specific to a
        collection.</flux:text>
    <flux:separator variant="subtle" />


    <livewire:post.list />
    <livewire:post.form />
    <livewire:common.dialog-confirm-modal :$isLoading @btn-delete-click="onConfirmDelete(id)"
        @post-status-toggled="onPostStatusToggled(id)" />
</flux:main>
