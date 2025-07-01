@props([
    'model' => null,
])

<div x-data="tiptapEditor({
    state: @entangle($attributes->wire('model'))
})" x-init="() => init($refs.editor)" wire:ignore {{ $attributes->whereDoesntStartWith('wire:model') }}
    class="block w-full shadow-xs [&[disabled]]:shadow-none border rounded-lg bg-white dark:bg-white/10 dark:[&[disabled]]:bg-white/[7%] **:data-[slot=content]:text-base! sm:**:data-[slot=content]:text-sm! **:data-[slot=content]:text-zinc-700 dark:**:data-[slot=content]:text-zinc-300 [&[disabled]_[data-slot=content]]:text-zinc-500 dark:[&[disabled]_[data-slot=content]]:text-zinc-400 border-zinc-200 border-b-zinc-300/80 dark:border-white/10">
    <x-editor.toolbar x-if="isLoaded()">
        <flux:dropdown>
            <flux:button size="xs" variant="subtle" icon:trailing="chevron-down" tooltip="Text Format">
                <flux:icon.h1 class="size-4" />
            </flux:button>
            <flux:menu>
                <flux:menu.item size="xs" @click="toggleParagraph()">Paragraph
                </flux:menu.item>
                <flux:menu.item size="xs" @click="toggleH2()" icon="h1">Heading 1
                </flux:menu.item>
                <flux:menu.item size="xs" @click="toggleH3()" icon="h2">Heading 2
                </flux:menu.item>
                <flux:menu.item size="xs" @click="toggleH4()" icon="h3">Heading 3
                </flux:menu.item>
            </flux:menu>
        </flux:dropdown>
        <flux:separator vertical />
        <flux:button size="xs" icon="bold" @click="toggleBold()" variant="subtle" tooltip="Bold"></flux:button>
        <flux:button size="xs" icon="italic" @click="toggleItalic()" variant="subtle" tooltip="Italic">
        </flux:button>
        <flux:button size="xs" icon="code-bracket" variant="subtle" tooltip="Code"></flux:button>
        <flux:button size="xs" icon="link" variant="subtle" @click="setLink()" tooltip="Link"></flux:button>
        <flux:button size="xs" icon="list-bullet" @click="toggleBulletList()" variant="subtle" tooltip="List">
        </flux:button>
        <flux:button size="xs" icon="numbered-list" @click="toggleOrderedList()" variant="subtle"
            tooltip="List Number"></flux:button>
        <flux:separator vertical />
        <flux:dropdown>
            <flux:button size="xs" variant="subtle" icon:trailing="chevron-down">
                <flux:icon.plus class="size-4" />
            </flux:button>
            <flux:menu>
                <flux:menu.item size="xs" icon="photo" @click="addImage()">Image
                </flux:menu.item>
                <flux:menu.item size="xs" icon="globe-alt">Youtube
                </flux:menu.item>
            </flux:menu>
        </flux:dropdown>
    </x-editor.toolbar>
    <div x-ref="editor" class="editor max-w-full"></div>
</div>
