@props(['disabled' => false])

<input @disabled($disabled)
    {{ $attributes->merge(['class' => 'border-gray-600 focus:border-gray-300 focus:ring-gray-500 rounded-md shadow-xs']) }}>
