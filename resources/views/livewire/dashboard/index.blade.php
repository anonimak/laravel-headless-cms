<?php

use Livewire\Volt\Component;
use function Livewire\Volt\{title, layout};
use App\Models\CovidStat;
use Carbon\Carbon;

layout('layouts.app');
title('Dashboard');
new class extends Component {
    // Properti publik untuk menyimpan data yang akan ditampilkan
    public $dataIndonesia;
    public $dataGlobal;
    public $potentialPercentage = 0; // Persentase potensi kenaikan kasus
    public $chartData = [
        'labels' => [],
        'indonesia' => [],
        'global' => [],
    ];

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
        // Ambil data akumulasi terakhir (H-1) diasumsikan dalam 3 tahun terakhir
        // Kita ambil data dari 3 tahun terakhir untuk memastikan kita mendapatkan data yang relevan
        $latestDate = Carbon::yesterday()->subYears(3)->format('Y-m-d');
        $this->dataIndonesia = CovidStat::where('region_name', 'Indonesia')->where('date', $latestDate)->first();
        $this->dataGlobal = CovidStat::where('region_name', 'Global')->where('date', $latestDate)->first();

        $timeline = CovidStat::where('date', '>=', Carbon::yesterday()->subYears(3)->subDays(29))
            ->orderBy('date', 'asc')
            ->get();

        $this->chartData = [
            'labels' => $timeline->where('region_name', 'Global')->pluck('date')->map(fn($d) => Carbon::parse($d)->format('d M')),
            'indonesia' => $timeline->where('region_name', 'Indonesia')->pluck('confirmed'),
            'global' => $timeline->where('region_name', 'Global')->pluck('confirmed'),
        ];

        $twoDaysAgo = Carbon::yesterday()->subYears(3)->subDay()->format('Y-m-d');
        $kasusBaruKemarin = $this->dataIndonesia ? $this->dataIndonesia->confirmed - (CovidStat::where('date', $twoDaysAgo)->where('region_name', 'Indonesia')->value('confirmed') ?? 0) : 0;
        $kasusBaruDuaHariLalu =
            (CovidStat::where('date', $twoDaysAgo)->where('region_name', 'Indonesia')->value('confirmed') ?? 0) -
            (CovidStat::where('date', Carbon::parse($twoDaysAgo)->subDay()->format('Y-m-d'))
                ->where('region_name', 'Indonesia')
                ->value('confirmed') ??
                0);

        // Hitung persentase potensi kenaikan kasus
        // Jika kasus baru dua hari lalu adalah 0, kita set persentase ke 0 untuk menghindari pembagian dengan nol
        // Jika tidak, kita hitung persentase kenaikan kasus baru hari ini dibandingkan dengan dua hari lalu
        // Rumus: ((Kasus Baru Hari Ini / Kasus Baru Dua Hari Lalu) - 1) * 100
        // Ini memberikan kita persentase kenaikan kasus baru dibandingkan dengan dua hari lalu.
        $this->potentialPercentage = $kasusBaruDuaHariLalu > 0 ? ($kasusBaruKemarin / $kasusBaruDuaHariLalu - 1) * 100 : 0;
    }
};

?>



<flux:main container>
    <div wire:poll.15s="loadStats" class="space-y-6">
        <h1 class="text-3xl font-bold">Dashboard COVID-19</h1>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <x-scoreboard-card title="Indonesia" :dataScore="$dataIndonesia" :potentialPercentage="$potentialPercentage">
                <div class="size-12 flex flex-col justify-center items-center rounded">
                    <div class="bg-red-700 flex-1 w-full rounded-t-full"></div>
                    <div class="bg-white flex-1 w-full rounded-b-full"></div>
                </div>
            </x-scoreboard-card>
            <x-scoreboard-card title="Global" :dataScore="$dataGlobal" :potentialPercentage="$potentialPercentage">
                <flux:icon.globe-alt class="size-12" />
            </x-scoreboard-card>
        </div>
        <div class="bg-zinc-900 border-2 dark:border-zinc-700 p-6 rounded-lg shadow">
            <h2 class="text-xl font-bold mb-4">Timeline Kasus Positif (30 Hari Terakhir)</h2>
            <canvas id="timelineChart"></canvas>
        </div>
    </div>
</flux:main>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('livewire:navigated', () => {
        const ctx = document.getElementById('timelineChart').getContext('2d');
        const chartData = @json($chartData);
        console.log(chartData);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.labels,
                datasets: [{
                        label: 'Indonesia',
                        data: chartData.indonesia,
                        borderColor: 'rgb(255, 99, 132)',
                        tension: 0.1
                    },
                    {
                        label: 'Global',
                        data: chartData.global,
                        borderColor: 'rgb(54, 162, 235)',
                        tension: 0.1
                    }
                ]
            }
        });
    })
</script>
