<?php

use App\Models\Category;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use App\Services\CategoryService;

new class extends Component {
    use WithPagination;

    #[Url(as: 'q', except: '')]
    public ?string $search = '';

    // Fungsi untuk mendapatkan daftar kategori
    public function with(CategoryService $service): array
    {
        return [
            'categories' => $service->getPaginated(search: $this->search, perPage: 10),
        ];
    }

    public function resetSearch(): void
    {
        $this->search = '';
        $this->resetPage();
    }

    // Atau cara yang lebih simpel dengan magic action $refresh
    #[On('category-deleted'), On('category-upserted')]
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
                                        Nama</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-xs font-medium tracking-wider text-left text-zinc-500 dark:text-zinc-200 uppercase">
                                        Slug</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-xs font-medium tracking-wider text-left text-zinc-500 dark:text-zinc-200 uppercase">
                                        Description</th>
                                    <th scope="col" class="relative px-6 py-3">
                                        <span class="sr-only">Action</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-zinc-700 divide-y divide-zinc-200 dark:divide-zinc-600">
                                @foreach ($categories as $category)
                                    <tr wire:key="{{ $category->id }}">
                                        <td class="px-6 py-4 whitespace-nowrap text-zinc-800 dark:text-zinc-200">
                                            {{ $category->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-zinc-800 dark:text-zinc-200">
                                            {{ $category->slug }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-zinc-800 dark:text-zinc-200">
                                            {{ $category->description }}
                                        </td>
                                        <td class="px-6 py-4 text-sm font-medium text-right whitespace-nowrap">

                                            <flux:dropdown>
                                                <flux:button variant="ghost" class="cursor-pointer" square
                                                    size="sm" icon="ellipsis-horizontal"></flux:button>

                                                <flux:menu>
                                                    <flux:menu.item class="cursor-pointer" icon="pencil"
                                                        wire:click="$dispatch('open-form', { categoryId: {{ $category->id }} })">
                                                        Edit
                                                    </flux:menu.item>
                                                    <flux:menu.separator />
                                                    <flux:menu.item
                                                        @click="$dispatch('open-delete-modal', {
                                                            'id': {{ $category->id }},
                                                            'title': 'Delete Category',
                                                            'description': 'Are you sure you want to delete this category {{ $category->name }} ? This action cannot be undone.',
                                                            'buttonText': 'Yes, Delete'
                                                        })"
                                                        class="cursor-pointer" variant="danger" icon="trash">
                                                        Delete
                                                    </flux:menu.item>
                                                </flux:menu>
                                            </flux:dropdown>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
        <div class="mt-4">
            {{ $categories->links('vendor.pagination.custom-simple-tailwind') }}
        </div>
    </div>
</div>
