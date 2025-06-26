<flux:dropdown position="top" align="start">
    <flux:profile 
        :name="$name" 
        :description="$email" 
        size="lg"
    />
    <flux:menu>
        <flux:menu.item 
            icon="user" 
            wire:navigate 
            href="{{ route('profile') }}">Profile</flux:menu.item>
        <flux:menu.separator />
        <flux:menu.item 
            icon="arrow-right-start-on-rectangle"
            wire:click="logout"
        >
            Logout
        </flux:menu.item>
    </flux:menu>
</flux:dropdown>