<flux:header container class="bg-zinc-50 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700">
    <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />
    <flux:brand href="#" name="Covid19">
        <x-slot name="logo" class="size-6 rounded-full bg-cyan-500 text-white text-xs font-bold">
            <flux:icon name="rocket-launch" variant="micro" />
        </x-slot>
    </flux:brand>
    <flux:navbar class="-mb-px max-lg:hidden">
        <flux:navbar.item icon="squares-2x2" href="{{ route('dashboard') }}" :current="request()->routeIs('dashboard')"
            wire:navigate>Dashboard
        </flux:navbar.item>
    </flux:navbar>
</flux:header>
