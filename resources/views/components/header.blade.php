<flux:header container class="bg-zinc-50 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />
        <flux:brand href="#" name="LaravelCMS">
            <x-slot name="logo" class="size-6 rounded-full bg-cyan-500 text-white text-xs font-bold">
                <flux:icon name="rocket-launch" variant="micro" />
            </x-slot>
        </flux:brand>
        <flux:navbar class="-mb-px max-lg:hidden">
            <flux:navbar.item icon="squares-2x2" href="{{ route('dashboard') }}"
                :current="request()->routeIs('dashboard')" wire:navigate>Dashboard
            </flux:navbar.item>
            <flux:navbar.item icon="inbox" href="{{ route('category') }}" :current="request()->routeIs('category')"
                wire:navigate>Category</flux:navbar.item>
            <flux:navbar.item icon="document-text" href="{{ route('post') }}" :current="request()->routeIs('post')"
                wire:navigate>Post</flux:navbar.item>
            <flux:navbar.item icon="window" href="#">Page</flux:navbar.item>
            <flux:separator vertical variant="subtle" class="my-2" />
            <flux:modal.trigger name="media-manager-modal">
                <flux:navbar.item icon="photo" href="#">Media Manager</flux:navbar.item>
            </flux:modal.trigger>
        </flux:navbar>
        <flux:spacer />

        <flux:dropdown position="top" align="start">
            <flux:profile circle avatar:name="{{ auth()->user()->name }}" size="lg" />
            <flux:menu>
                <flux:menu.item icon="user" wire:navigate href="{{ route('profile') }}">{{ auth()->user()->name }}
                </flux:menu.item>
                <flux:menu.separator />
                <flux:menu.item icon="arrow-right-start-on-rectangle" wire:click="logout">
                    Logout
                </flux:menu.item>
            </flux:menu>
        </flux:dropdown>
    </flux:header>