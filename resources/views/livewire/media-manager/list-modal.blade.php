<?php

// create volt component
use Livewire\Volt\Component;
use App\Services\MediaManagerService;
use Livewire\Attributes\{Reactive, On};
use Livewire\WithFileUploads;

new class extends Component {
    use WithFileUploads;
    public bool $isLoading = true;
    public array $mediaList = [];

    public function mount(MediaManagerService $service): void
    {
        $this->mediaList = $service->getMediaList();
        $this->isLoading = false;
    }

    #[On('media-uploaded')]
    public function updatedMediaList(MediaManagerService $service): void
    {
        $this->mediaList = $service->getMediaList();
    }

    #[On('delete-media')]
    public function deleteMedia(MediaManagerService $service, $index): void
    {
        if (isset($this->mediaList[$index])) {
            $media = $this->mediaList[$index];
            if ($service->deleteMediaFile($media->name)) {
                unset($this->mediaList[$index]);
                $this->dispatch('show-flash', [
                    'message' => 'Media deleted successfully.',
                    'type' => 'success',
                ]);
            } else {
                $this->dispatch('show-flash', [
                    'message' => 'Failed to delete media file.',
                    'type' => 'error',
                ]);
                return;
            }
        } else {
            $this->dispatch('show-flash', [
                'message' => 'Media not found.',
                'type' => 'error',
            ]);
        }
    }
};

?>
<div x-data="{
    isLoading: @entangle('isLoading'),
    mediaList: @entangle('mediaList'),
    selectedMedia: null,

    init() {
        console.log('Media Manager initialized');
        if (this.isLoading) {
            console.log('Loading media list...');
        } else {
            console.log('Media list loaded:', this.mediaList);
        }

    },
    selectingMedia(index) {
        console.log('Selecting media at index:', index);
        if (this.selectedMedia === index) {
            this.selectedMedia = null;
            return;
        }
        this.selectedMedia = index;
    },
    copy() {
        if (this.selectedMedia !== null) {
            const media = this.mediaList[this.selectedMedia];
            navigator.clipboard.writeText(media.url).then(() => {
                console.log('Media path copied to clipboard:', media.url);
                Livewire.dispatch('show-flash', {
                    message: 'Media path copied to clipboard.',
                    type: 'success'
                });
            }).catch(err => {
                console.error('Failed to copy media path:', err);
                Livewire.dispatch('show-flash', {
                    message: 'Failed to copy media path.',
                    type: 'error'
                });
            });
        }
    },
    downloadSelectedMedia() {
        if (this.selectedMedia !== null) {
            const media = this.mediaList[this.selectedMedia];
            const link = document.createElement('a');
            link.href = media.url;
            link.setAttribute('download', media.filename);

            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            $dispatch('show-flash', {
                message: 'Downloading media: ' + media.name,
                type: 'info'
            });
        } else {
            $dispatch('show-flash', {
                message: 'No media selected for download.',
                type: 'warning'
            });
        }
    },
    deleteSelectedMedia() {
        if (this.selectedMedia !== null) {
            const media = this.mediaList[this.selectedMedia];
            {{-- using window for confir --}}
            if (confirm('Are you sure you want to delete this media: ' + media.name + '?')) {
                $dispatch('delete-media', {
                    index: this.selectedMedia,
                });
                this.mediaList.splice(this.selectedMedia, 1);
                this.selectedMedia = null;
            } else {
                $dispatch('show-flash', {
                    message: 'Media deletion cancelled.',
                    type: 'info'
                });
            }
        } else {
            $dispatch('show-flash', {
                message: 'No media selected for deletion.',
                type: 'warning'
            });
        }
    }
}">
    <flux:modal name="media-manager-modal" class="!max-w-6xl" wire:ignore.self>
        <div class="p-4">
            <flux:heading size="lg">Media Manager</flux:heading>
            <flux:text class="mt-2">
                <p>Manage your media files here. You can upload new files or select existing ones.</p>
            </flux:text>
            {{-- loop media lists from $form->mediaList --}}
            <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 h-96 overflow-y-scroll">
                @foreach ($mediaList as $index => $media)
                    <x-media-item :media="$media" :index="$index" @click="selectingMedia({{ $index }})"
                        wire:key="media-item-{{ $index }}" />
                @endforeach
            </div>
            <div class="flex w-full items-center justify-end mt-4">
                <flux:button.group>
                    <flux:button class="cursor-pointer" size="sm" icon="arrow-up-tray"
                        x-on:click="$flux.modal('form-upload-modal').show()">
                        Upload New
                    </flux:button>
                    <flux:button class="cursor-pointer" size="sm" icon="arrow-down-tray"
                        x-bind:disabled="selectedMedia == null" x-on:click="downloadSelectedMedia">
                        Download
                    </flux:button>
                    <flux:button class="cursor-pointer" size="sm" icon="clipboard" x-on:click="copy"
                        x-bind:disabled="selectedMedia == null">
                        Copy
                    </flux:button>
                    <flux:button class="cursor-pointer" size="sm" variant="danger" icon="trash"
                        x-on:click="deleteSelectedMedia" x-bind:disabled="selectedMedia == null">
                        Delete Selection
                    </flux:button>
                </flux:button.group>
            </div>
        </div>
        <livewire:media-manager.upload-modal />
    </flux:modal>
