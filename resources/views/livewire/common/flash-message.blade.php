<?php

use Livewire\Volt\Component;

new class extends Component {};

?>

<div x-data="{ show: false, message: 'test', type: 'success' }"
    x-on:show-flash.window="
        if ($event.detail.length > 0) {
            console.log('Flash message:', $event.detail[0]);
            show = true;
            message = $event.detail[0].message;
            type = $event.detail[0].type || 'success';
            setTimeout(() => show = false, 3000);
        }
        "
    x-show="show" x-transition style="display: none;" class="fixed bottom-4 right-4 z-[1100] w-full md:w-md">
    <flux:callout x-on:click="show = false">
        <x-slot name="icon">
            <template x-if="type === 'success'">
                <flux:icon name="check-circle" class="text-green-500" />
            </template>
            <template x-if="type === 'error'">
                <flux:icon name="x-circle" class="text-red-500" />
            </template>
            <template x-if="type === 'warning'">
                <flux:icon name="exclamation-triangle" class="text-yellow-500" />
            </template>
            <template x-if="type === 'info'">
                <flux:icon name="information-circle" class="text-blue-500" />
            </template>
        </x-slot>
        <flux:callout.heading x-text="message">
        </flux:callout.heading>
    </flux:callout>
</div>
