<?php

use App\Models\Page;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use App\Services\PageService;

new class extends Component {
    use WithPagination;

    #[Url(as: 'q', except: '')]
    public ?string $search = '';

    // Fungsi untuk mendapatkan daftar kategori
    public function with(PageService $service): array
    {
        return [
            'pages' => $service->getPaginated(search: $this->search, perPage: 4),
        ];
    }

    public function resetSearch(): void
    {
        $this->search = '';
        $this->resetPage();
    }

    #[On('page-deleted'), On('page-upserted')]
    public function placeholder()
    {
        $this->search = '';
    }
};

?>

<div>
    <div class="mt-8">
        <div class="flex flex-col bg-zinc-50 dark:bg-zinc-900 shadow-xs sm:rounded-lg">
            <div class="inline-block min-w-full pt-2 align-middle">
                <div class="overflow-hidden border-b border-zinc-200 dark:border-zinc-600 sm:rounded-lg">
                    <div class=" px-4 py-4 sm:px-6 flex justify-between items-center">
                        <div class="flex items-center space-x-2 md:w-xs">
                            <flux:input wire:model.live.debounce.150ms="search" size="sm" icon="magnifying-glass"
                                placeholder="Search orders">
                                <x-slot name="iconTrailing">
                                    <flux:button wire:click="resetSearch" wire:show="search" size="sm"
                                        variant="subtle" icon="x-mark" class="-mr-1" />
                                </x-slot>
                            </flux:input>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-600">
                            <thead class="bg-zinc-50 dark:bg-zinc-800">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-xs font-medium tracking-wider text-left text-zinc-500 dark:text-zinc-200 uppercase">
                                        Title</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-xs font-medium tracking-wider text-left text-zinc-500 dark:text-zinc-200 uppercase">
                                        Status</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-xs font-medium tracking-wider text-left text-zinc-500 dark:text-zinc-200 uppercase">
                                        Published At</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-xs font-medium tracking-wider text-left text-zinc-500 dark:text-zinc-200 uppercase">
                                        Created At</th>
                                    <th scope="col" class="relative px-6 py-3">
                                        <span class="sr-only">Action</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-zinc-700 divide-y divide-zinc-200 dark:divide-zinc-600">
                                @forelse ($pages as $page)
                                    <tr wire:key="{{ $page->id }}">
                                        <td class="px-6 py-4 whitespace-nowrap text-zinc-800 dark:text-zinc-200">
                                            {{ $page->title }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-zinc-800 dark:text-zinc-200">
                                            <flux:badge size="sm"
                                                color="{{ $page->status === 'published' ? 'green' : 'yellow' }}">
                                                {{ $page->status }}
                                            </flux:badge>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-zinc-800 dark:text-zinc-200">
                                            @if ($page->published_at)
                                                {{ $page->published_at->format('Y-m-d H:i:s') }}
                                            @else
                                                <span class="text-zinc-500">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-zinc-800 dark:text-zinc-200">
                                            {{ $page->created_at->format('Y-m-d H:i:s') }}
                                        </td>
                                        <td class="px-6 py-4 text-sm font-medium text-right whitespace-nowrap">

                                            <flux:dropdown>
                                                <flux:button variant="ghost" class="cursor-pointer" square
                                                    size="sm" icon="ellipsis-horizontal"></flux:button>

                                                <flux:menu>
                                                    {{-- if status draft  --}}
                                                    <flux:menu.item class="cursor-pointer"
                                                        icon="{{ $page->status === 'draft' ? 'arrow-up-on-square' : 'arrow-down-on-square' }}"
                                                        @click="$dispatch('open-dialog-modal', {
                                                            'id': {{ $page->id }},
                                                            'title': '{{ $page->status === 'draft' ? 'Publish Page' : 'Unpublish Page' }}',
                                                            'description': 'Are you sure you want to {{ $page->status === 'draft' ? 'publish' : 'unpublish' }} {{ $page->title }} ?',
                                                            'buttonText':'Yes',
                                                            'buttonVariant': '{{ $page->status === 'draft' ? 'primary' : 'default' }}',
                                                            'dispatchEvent': 'page-status-toggled'
                                                        })">
                                                        {{ $page->status === 'draft' ? 'Publish' : 'Unpublish' }}
                                                    </flux:menu.item>
                                                    <flux:menu.item class="cursor-pointer" icon="pencil"
                                                        wire:click="$dispatch('open-form', { pageId: {{ $page->id }} })">
                                                        Edit
                                                    </flux:menu.item>
                                                    <flux:menu.separator />
                                                    <flux:menu.item
                                                        @click="$dispatch('open-dialog-modal', {
                                                            'id': {{ $page->id }},
                                                            'title': 'Delete Page',
                                                            'description': 'Are you sure you want to delete this page {{ $page->name }} ? This action cannot be undone.',
                                                            'buttonText': 'Yes, Delete',
                                                            'buttonVariant': 'danger',
                                                            'dispatchEvent': 'btn-delete-click'
                                                        })"
                                                        class="cursor-pointer" variant="danger" icon="trash">
                                                        Delete
                                                    </flux:menu.item>
                                                </flux:menu>
                                            </flux:dropdown>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5"
                                            class="px-6 py-4 text-center text-zinc-500 dark:text-zinc-400">
                                            No pages found.
                                        </td>
                                    </tr>
                                @endforelse

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
        <div class="mt-4">
            {{ $pages->links('vendor.pagination.custom-simple-tailwind') }}
        </div>
    </div>
</div>
