<?php

namespace App\Console\Commands;

use App\Models\CovidStat;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SyncCovidData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-covid-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ambil data COVID-19 dari API dan simpan ke database, diasumsikan data yang diambil 3 tahun dari sekarang hingga 30 hari ke belakang.';

    /**
     * Execute the console command.
     */

    protected $httpClient;

    public function __construct()
    {
        parent::__construct();
        $this->httpClient = Http::withOptions([
            'verify' => config('services.http.verify', false),
            'timeout' => config('services.http.timeout', 30),
            'connect_timeout' => config('services.http.connect_timeout', 10),
        ]);
    }

    public function handle()
    {
        $this->info('Mulai sinkronisasi data COVID-19...');

        for ($i = 30; $i >= 1; $i--) {
            $date = Carbon::now()->subYears(3)->subDays($i - 1)->format('Y-m-d');

            // Ambil data Indonesia (IDN)
            $responseIdn = $this->httpClient->get('https://covid-api.com/api/reports/total', ['date' => $date, 'iso' => 'IDN']);
            if ($responseIdn->successful() && isset($responseIdn->json()['data'])) {
                $data = $responseIdn->json()['data'];
                CovidStat::updateOrCreate(
                    ['date' => $date, 'region_iso' => 'IDN'],
                    [
                        'region_name' => 'Indonesia',
                        'confirmed' => $data['confirmed'],
                        'deaths' => $data['deaths'],
                        'recovered' => $data['recovered'] ?? 0
                    ]
                );
            }

            // Ambil data Dunia (Global)
            $responseGlobal = $this->httpClient->get('https://covid-api.com/api/reports/total', ['date' => $date]);
            if ($responseGlobal->successful() && isset($responseGlobal->json()['data'])) {
                $data = $responseGlobal->json()['data'];
                CovidStat::updateOrCreate(
                    ['date' => $date, 'region_name' => 'Global'],
                    [
                        'region_iso' => null,
                        'confirmed' => $data['confirmed'],
                        'deaths' => $data['deaths'],
                        'recovered' => $data['recovered'] ?? 0,
                    ]
                );
            }
            $this->info("Sinkronisasi data COVID-19 untuk tanggal: $date");
        }


        $this->info('Sinkronisasi data COVID-19 selesai.');
        return 0;
    }
}
