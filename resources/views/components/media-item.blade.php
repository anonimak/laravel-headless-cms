{{-- resources/views/components/media-item.blade.php --}}

@props(['media', 'index'])
<div :class="{
    'border-blue-500': selectedMedia == {{ $index }},
    'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600': selectedMedia !=
        {{ $index }}
}"
    {{ $attributes->merge(['class' => 'relative group cursor-pointer h-48 rounded-lg overflow-hidden border-2 transition']) }}>
    <img src="{{ $media->url }}" alt="{{ $media->name }}" class="w-full h-full object-cover rounded-lg">
    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition duration-200 rounded-lg"></div>
    <div class="absolute bottom-0 left-0 right-0 p-2 text-white bg-black bg-opacity-50 rounded-b-lg truncate">
        <flux:tooltip position="bottom">
            <flux:text class="text-sm truncate">
                {{ $media->name }}
            </flux:text>
        </flux:tooltip>
    </div>
</div>
