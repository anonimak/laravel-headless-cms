<?php

use Livewire\Volt\Component;
use function Livewire\Volt\{title, layout};
use App\Models\{Category, Page, Post};

layout('layouts.app');
title('Dashboard');
new class extends Component {
    // Properti publik untuk menyimpan data yang akan ditampilkan
    public int $categoryCount = 0;
    public int $publishedPostCount = 0;
    public int $unpublishedPostCount = 0;
    public int $pageCount = 0;

    /**
     * mount() dijalankan saat komponen pertama kali di-load.
     * Kita memuat statistik awal di sini.
     */
    public function mount(): void
    {
        $this->loadStats();
    }

    public function loadStats(): void
    {
        // Asumsi Anda memiliki model Category, Post, dan Page
        $this->categoryCount = Category::count();
        $this->pageCount = Page::count();

        // Asumsi model Post memiliki kolom boolean 'is_published'
        $this->publishedPostCount = Post::where('status', Post::STATUS_DRAFT)->count();
        $this->unpublishedPostCount = Post::where('status', Post::STATUS_PUBLISHED)->count();
    }
};

?>



<flux:main container>
    <div wire:poll.15s="loadStats">
        <!-- Grid Layout untuk Kartu Skor -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

            <!-- Kartu 1: Jumlah Kategori -->
            <x-scoreboard-card title="Categories" :value="$categoryCount" color="blue">
                <flux:icon.tag size="xl" />
            </x-scoreboard-card>

            <!-- Kartu 2: Post Terpublish -->
            <x-scoreboard-card title="Published Post" :value="$publishedPostCount" color="green">
                <flux:icon.check-circle size="xl" />
            </x-scoreboard-card>
            <!-- Kartu 3: Post Draft -->
            <x-scoreboard-card title="Unpublish Post" :value="$unpublishedPostCount" color="yellow">
                <flux:icon.queue-list size="xl" />
            </x-scoreboard-card>


            <!-- Kartu 4: Jumlah Halaman -->
            <x-scoreboard-card title="Pages" :value="$pageCount" color="purple">
                <flux:icon.window size="xl" />
            </x-scoreboard-card>
        </div>
    </div>
</flux:main>
