<?php

use Livewire\Volt\Component;
use App\Models\Category; // Ganti dengan model yang sesuai
use Livewire\Attributes\{Reactive};

new class extends Component {
    #[Reactive]
    public bool $isLoading = true;
};

?>
<div x-data="{
    id: null,
    title: '',
    description: '',
    buttonText: '',
    reset() {
        this.id = null;
        this.title = '';
        this.description = '';
        this.buttonText = '';
    },
    init() {
        this.reset();
    }
}"
    @open-delete-modal.window="
        id = $event.detail.id;
        console.log('ID:', id);
        title = $event.detail.title || 'Anda yakin?';
        description = $event.detail.description || 'Aksi ini tidak bisa dibatalkan.';
        buttonText = $event.detail.buttonText || 'Ya, Hapus';
        
        $flux.modal('delete-confirmation-modal').show()
    ">
    <flux:modal name="delete-confirmation-modal" class="min-w-[22rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg" x-text="title"></flux:heading>
                <flux:text class="mt-2">
                    <p x-text="description"></p>
                </flux:text>
            </div>

            <div class="flex justify-end gap-2">
                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>
                <flux:button type="button" class="cursor-pointer" variant="danger" :disabled="$isLoading"
                    x-on:click="() => $dispatch('btn-delete-click', { id:id })">
                    <span x-text="buttonText"></span>
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
