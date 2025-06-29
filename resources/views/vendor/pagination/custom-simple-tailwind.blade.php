@if ($paginator->hasPages())
<nav
    role="navigation"
    aria-label="Pagination Navigation"
    class="flex justify-end"
>
    <div class="flex items-center bg-white border border-zinc-200 rounded-[8px] p-[1px] dark:bg-white/10 dark:border-white/10">
        <flux:button
            size="sm"
            icon="chevron-left" 
            class="flex justify-center items-center size-8 sm:size-6 rounded-[6px]"
            :disabled="$paginator->onFirstPage()" 
            wire:click="previousPage"
            :loading="false"
            variant="ghost"
            wire:navigate
        />
        <flux:button icon="chevron-right"
            size="sm"
            class="flex justify-center items-center size-8 sm:size-6 rounded-[6px]"
            wire:click="nextPage"
            :loading="false"
            :disabled="!$paginator->hasMorePages()"
            variant="ghost"
            wire:navigate
        />
    </div>
</nav>
@endif
