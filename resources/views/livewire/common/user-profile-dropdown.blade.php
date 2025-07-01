<flux:dropdown position="top" align="start">
    <flux:profile circle avatar:name="{{ $name }}" size="lg" />
    <flux:menu>
        <flux:menu.item icon="user" wire:navigate href="{{ route('profile') }}">{{ $name }}
        </flux:menu.item>
        <flux:menu.separator />
        <flux:menu.item icon="arrow-right-start-on-rectangle" wire:click="logout" class="cursor-pointer">
            Logout
        </flux:menu.item>
    </flux:menu>
</flux:dropdown>
