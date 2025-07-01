<?php

// create volt component
use Livewire\Volt\Component;
use App\Services\MediaManagerService;
use Livewire\Attributes\{Reactive, On};
use Livewire\WithFileUploads;

new class extends Component {
    use WithFileUploads;

    #[Reactive(['media' => 'file|mime:jpg,jpeg,png,gif,webp,svg|max:10240'])]
    public $media;

    public function mount(MediaManagerService $service): void
    {
        $this->isLoading = false;
    }

    // watch if media is filled then save to media manager service
    #[On('media')]
    public function updatedMedia(MediaManagerService $service): void
    {
        if ($this->media) {
            $service->uploadMediaFile($this->media);
            // dispatch an event to notify the media manager that a new media has been uploaded
            $this->dispatch('media-uploaded', [
                'media' => $this->media,
            ]);
            // show a flash message
            $this->dispatch('show-flash', [
                'message' => 'Media uploaded successfully.',
                'type' => 'success',
            ]);
            // reset the media input
            $this->media = null;
            // close the modal
            Flux::modal('form-upload-modal')->close();
        }
    }
};
?>


<flux:modal name="form-upload-modal" class="!max-w-2xl">
    <div class="p-2" x-data="{ uploading: false, progress: 0, media: @entangle('media') }" x-on:livewire-upload-start="uploading = true"
        x-on:livewire-upload-finish="uploading = false" x-on:livewire-upload-cancel="uploading = false"
        x-on:livewire-upload-error="uploading = false" x-on:livewire-upload-progress="progress = $event.detail.progress">
        <flux:heading size="lg">Upload New Media</flux:heading>
        <div x-show="!uploading" class="mt-4">
            <flux:text>
                <p>Select a file to upload. Supported formats are JPG, PNG, GIF, WEBP, and SVG.</p>
            </flux:text>
            <flux:input class="mt-4" wire:model="media" type="file" as="button" size="sm"
                icon="arrow-up-tray" label="Upload" />
        </div>


        <!-- Progress Bar from tailwind -->
        <div x-show="uploading" class="mt-4 text-center">
            <flux:text>
                <p>Uploading to media. Supported formats are JPG, PNG, GIF, WEBP, and SVG.</p>
            </flux:text>
            <div class="w-full bg-zinc-200 rounded-full h-2.5 dark:bg-zinc-700">
                <div class="bg-blue-600 h-2.5 rounded-full transition-all duration-300"
                    x-bind:style="'width: ' + progress + '%'"></div>
            </div>
            <flux:text class="mt-2 text-sm text-gray-500" x-text="'Uploading... ' + progress + '%'">
            </flux:text>
            {{-- cancel --}}
            <flux:button class="mt-2" size="sm" wire:click="$cancelUpload('media')">
                Cancel
            </flux:button>
        </div>

    </div>
</flux:modal>
