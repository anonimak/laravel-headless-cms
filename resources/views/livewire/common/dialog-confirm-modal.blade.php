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
    buttonVariant: 'danger',
    dispatchEvent: 'btn-delete-click',
    reset() {
        this.id = null;
        this.title = '';
        this.description = '';
        this.buttonText = '';
        this.buttonVariant = 'danger';
        this.dispatchEvent = 'btn-delete-click';
    },
    onClick() {
        if (this.id) {
            console.log('Dispatching event:', this.dispatchEvent, 'with ID:', this.id);
            this.$dispatch(this.dispatchEvent, { id: this.id });
        } else {
            console.error('ID is not set.');
        }
    },
    init() {
        this.reset();
    }
}"
    @open-dialog-modal.window="
        id = $event.detail.id;
        console.log('ID:', id);
        title = $event.detail.title || 'Anda yakin?';
        description = $event.detail.description || 'Aksi ini tidak bisa dibatalkan.';
        buttonText = $event.detail.buttonText || 'Ya, Hapus';
        buttonVariant = $event.detail.buttonVariant || 'danger';
        dispatchEvent = $event.detail.dispatchEvent || 'btn-delete-click';
        console.log('Dispatch Event:', dispatchEvent);
        $flux.modal('dialog-confirm-modal').show()
        $nextTick()
    ">
    <flux:modal name="dialog-confirm-modal" class="min-w-[22rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg" x-text="title"></flux:heading>
                <flux:text class="mt-2">
                    <p x-text="description"></p>
                </flux:text>
            </div>

            <div class="flex justify-end gap-2">
                <flux:modal.close>
                    <flux:button class="cursor-pointer" variant="ghost">Cancel</flux:button>
                </flux:modal.close>
                <template x-if="buttonVariant === 'danger'">
                    <flux:button class="cursor-pointer" type="button" variant="danger" loading :disabled="$isLoading"
                        @click="onClick">
                        <span x-text="buttonText"></span>
                    </flux:button>
                </template>

                <template x-if="buttonVariant === 'primary'">
                    <flux:button class="cursor-pointer" type="button" variant="primary" loading :disabled="$isLoading"
                        @click="onClick">
                        <span x-text="buttonText"></span>
                    </flux:button>
                </template>

                <template x-if="buttonVariant === 'default'">
                    <flux:button class="cursor-pointer" type="button" loading :disabled="$isLoading" @click="onClick">
                        <span x-text="buttonText"></span>
                    </flux:button>
                </template>

            </div>
        </div>
    </flux:modal>
</div>
