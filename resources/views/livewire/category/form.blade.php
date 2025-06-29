<?php
use Livewire\Volt\Component;
use App\Livewire\Forms\CategoryForm;
use Livewire\Attributes\{Reactive, Url, On};
use App\Models\Category;
use App\Services\CategoryService;

new class extends Component {
    public CategoryForm $form;

    public function save(CategoryService $service)
    {
        try {
            $this->form->save($service);
            $this->dispatch('show-flash', [
                'message' => 'Category saved successfully.',
                'type' => 'success',
            ]);
            $this->dispatch('category-upserted');
            Flux::modal('form-category')->close();
            $this->form->resetForm();
        } catch (\Exception $e) {
            $this->dispatch('show-flash', [
                'message' => 'Failed to save category: ' . $e->getMessage(),
                'type' => 'error',
            ]);
            return;
        }
    }

    #[On('open-form')]
    public function onOpen($category = null): void
    {
        if (is_array($category)) {
            $category = Category::find($category['id']);
        } elseif ($category instanceof Category) {
            $category = $category;
        } else {
            $category = new Category();
        }
        $this->form->setCategory($category);
        Flux::modal('form-category')->show();
    }

    public function onCloseForm(): void
    {
        $this->form->resetForm();
    }
};
?>

<flux:modal name="form-category" @close="onCloseForm" class="md:w-96">
    <form wire:submit="save">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Form Category') }}</flux:heading>
            </div>
            <flux:field>
                <flux:label badge="Required" label="Name" placeholder="Category name">Name</flux:label>
                <flux:input wire:model="form.name" type="text" />
                @error('form.name')
                    <flux:error name="name" message="{{ $message }}" />
                @enderror
            </flux:field>
            {{-- flux field for slug --}}
            <flux:field>
                <flux:label badge="Required" label="Slug" placeholder="Category slug">Slug</flux:label>
                <flux:input wire:model="form.slug" type="text" />
                @error('form.slug')
                    <flux:error name="slug" message="{{ $message }}" />
                @enderror
            </flux:field>
            {{-- textarea --}}
            <flux:field>
                <flux:label label="Description" placeholder="Category description">Description</flux:label>
                <flux:textarea wire:model="form.description" rows="3" />
                <flux:error name="description" />
            </flux:field>
            <div class="flex">
                <flux:spacer />
                <flux:button type="submit" variant="primary">Save</flux:button>
            </div>
        </div>
    </form>
</flux:modal>
