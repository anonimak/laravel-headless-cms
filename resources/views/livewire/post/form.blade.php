<?php
use Livewire\Volt\Component;
use App\Livewire\Forms\PostForm;
use Livewire\Attributes\{Reactive, Url, On};
use App\Models\Post;
use App\Services\PostService;

new class extends Component {
    public PostForm $form;
    public string $postContent = '';

    public function save(PostService $service)
    {
        try {
            $this->form->save($service);
            $this->dispatch('show-flash', [
                'message' => 'Post saved successfully.',
                'type' => 'success',
            ]);
            $this->dispatch('post-upserted');
            Flux::modal('form-post')->close();
            $this->form->resetForm();
        } catch (\Exception $e) {
            $this->dispatch('show-flash', [
                'message' => 'Failed to save post: ' . $e->getMessage(),
                'type' => 'error',
            ]);
            return;
        }
    }

    #[On('open-form')]
    public function onOpen(PostService $service, ?int $postId = null): void
    {
        if ($postId) {
            $post = $service->getById($postId);
        } else {
            $post = new Post();
        }
        $this->form->setPost($post);
        Flux::modal('form-post')->show();
    }

    public function onCloseForm(): void
    {
        $this->form->resetForm();
    }
};
?>

<flux:modal name="form-post" @close="onCloseForm" variant="flyout" class="w-full !ml-0 !max-w-full !p-0" dismissible="false">
    <flux:header class="bg-zinc-50 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />
        <flux:brand href="#" logo="https://fluxui.dev/img/demo/logo.png" name="Acme Inc."
            class="max-lg:hidden dark:hidden" />
        <flux:brand href="#" logo="https://fluxui.dev/img/demo/dark-mode-logo.png" name="Acme Inc."
            class="max-lg:hidden! hidden dark:flex" />
        <flux:navbar class="-mb-px max-lg:hidden">
            <flux:button size="sm" variant="filled" class="cursor-pointer">Save</flux:button>
            <flux:dropdown>
                <flux:button size="sm" variant="primary" icon:trailing="chevron-down" class="cursor-pointer">
                    Published</flux:button>
                <flux:menu>
                    <flux:menu.item class="cursor-pointer" icon="arrow-down-on-square" variant="danger">Unpublish
                    </flux:menu.item>
                    <flux:menu.separator />
                    <flux:menu.item class="cursor-pointer" icon="document-duplicate">Duplicate</flux:menu.item>
                </flux:menu>
            </flux:dropdown>
            <flux:separator vertical variant="subtle" class="my-2" />
            <flux:button size="sm" variant="danger" icon="trash" class="cursor-pointer">Delete Entry
            </flux:button>
        </flux:navbar>
        <flux:spacer />
    </flux:header>
    <div class="flex h-screen overflow-hidden overflow-y-scroll" x-data="{
        isResizing: false,
        liveContent: @entangle('postContent').live,
        leftPanelWidth: localStorage.getItem('leftPanelWidth') || window.innerWidth / 2,
        onMouseDown(event) {
            event.preventDefault();
            this.isResizing = true;
        },
    
        onMouseMove(event) {
            if (!this.isResizing) {
                return;
            }
            const containerOffsetLeft = this.$el.getBoundingClientRect().left;
    
            let newWidth = event.clientX - containerOffsetLeft;
            const minWidth = 350;
            const maxWidth = window.innerWidth - 350;
    
            if (newWidth < minWidth) {
                newWidth = minWidth;
                this.isResizing = false;
            }
            if (newWidth > maxWidth) {
                newWidth = maxWidth;
                this.isResizing = false;
            }
    
            this.leftPanelWidth = newWidth;
            this.$nextTick(() => {
                localStorage.setItem('leftPanelWidth', this.leftPanelWidth);
            });
        },
    
        onMouseUp() {
            this.isResizing = false;
            localStorage.setItem('leftPanelWidth', this.leftPanelWidth);
        },
        htmlOutput() {
            return marked.parse(this.liveContent || '', { sanitize: true });
        }
    
    }"
        @mousemove.window="onMouseMove($event)" @mouseup.window="onMouseUp()" :class="{ 'no-select': isResizing }">
        <div class="shrink-0 p-6" :style="`width: ${leftPanelWidth}px`">
            <form wire:submit="save">
                <div class="space-y-6">
                    <div>
                        <flux:heading size="lg">{{ __('Form Post') }}</flux:heading>
                    </div>
                    <flux:field>
                        <flux:label badge="Required" label="Name" placeholder="Post title">Title</flux:label>
                        <flux:input wire:model="form.title" type="text" />
                        @error('form.title')
                            <flux:error name="title" message="{{ $message }}" />
                        @enderror
                    </flux:field>
                    {{-- flux field for slug --}}
                    <flux:field>
                        <flux:label badge="Required" label="Slug" placeholder="Post slug">Slug</flux:label>
                        <flux:input wire:model="form.slug" type="text" />
                        @error('form.slug')
                            <flux:error name="slug" message="{{ $message }}" />
                        @enderror
                    </flux:field>
                    {{-- textarea --}}
                    <flux:field>
                        <flux:label label="Description" placeholder="Post content">Description</flux:label>
                        <x-editor wire:model.live="postContent" />
                        <flux:error name="content" />
                    </flux:field>
                    <div class="flex">
                        <flux:spacer />
                        <flux:button type="submit" variant="primary">Save</flux:button>
                    </div>
                </div>
            </form>
        </div>

        <div class="w-1 cursor-col-resize bg-zinc-300 dark:bg-zinc-700 hover:bg-blue-600 transition-colors"
            @mousedown="onMouseDown($event)"></div>

        <div class="p-6 flex-grow bg-zinc-50 dark:bg-zinc-800">
            {{-- live preview postContent --}}
            <div class="prose prose-2xl dark:prose-invert mt-4">
                <div class="mt-4" x-html="htmlOutput">
                </div>
            </div>
        </div>
        <style>
            .no-select {
                -webkit-touch-callout: none;
                -webkit-user-select: none;
                -khtml-user-select: none;
                -moz-user-select: none;
                -ms-user-select: none;
                user-select: none;
            }
        </style>
</flux:modal>
