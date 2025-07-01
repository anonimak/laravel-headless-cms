@props(['title', 'value', 'iconSvgPath', 'color' => 'blue'])

@php
    $colorClasses = [
        'blue' => 'bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400',
        'green' => 'bg-green-100 dark:bg-green-900/50 text-green-600 dark:text-green-400',
        'yellow' => 'bg-yellow-100 dark:bg-yellow-900/50 text-yellow-600 dark:text-yellow-400',
        'purple' => 'bg-purple-100 dark:bg-purple-900/50 text-purple-600 dark:text-purple-400',
    ];
@endphp

<div class="bg-white dark:bg-zinc-900 p-6 rounded-lg shadow border-2 dark:border-zinc-700 border-zinc-300">
    <div class="flex justify-between items-start">
        <div>
            <h2 class="text-sm font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ $title }}
            </h2>
            <p class="text-4xl font-extrabold text-zinc-900 dark:text-white mt-2">{{ $value }}</p>
        </div>
        <div class="p-3 rounded-full {{ $colorClasses[$color] }}">
            {{ $slot }}
        </div>
    </div>
</div>
