# Laravel COVID-19 Dashboard

Aplikasi dashboard COVID-19 yang menampilkan statistik real-time kasus Indonesia dan Global dengan visualisasi chart timeline.

## Tools dan Framework

- **Backend**: Laravel 11 dengan Livewire Volt
- **Frontend**: Blade Templates + Flux UI Components
- **Database**: MySQL/SQLite
- **Visualisasi**: Chart.js
- **HTTP Client**: Laravel HTTP Facade
- **Styling**: Tailwind CSS

## Struktur Folder Laravel 11

```
laravel-headless-cms/
├── app/
│   ├── Console/
│   │   └── Commands/
│   │       └── SyncCovidData.php          # Command sync data COVID
│   ├── Http/
│   │   ├── Controllers/                   # Controllers (jika diperlukan)
│   │   └── Middleware/
│   ├── Models/
│   │   └── CovidStat.php                  # Model data COVID
├── bootstrap/
├── config/
│   ├── app.php
│   └── services.php                       # HTTP config settings
├── database/
│   ├── migrations/
│   │   └── xxxx_create_covid_stats_table.php
│   └── seeders/
├── public/
├── resources/
│   ├── css/
│   │   └── app.css                        # Tailwind CSS
│   ├── js/
│   │   └── app.js                         # JavaScript assets
│   └── views/
│       ├── components/
│       │   └── scoreboard-card.blade.php  # Reusable component
│       ├── layouts/
│       │   └── app.blade.php              # Main layout
│       └── livewire/
│           └── dashboard/
│               └── index.blade.php        # Dashboard Livewire Volt
├── routes/
│   ├── web.php                            # Web routes
│   └── console.php                        # Scheduled commands
```

## Alur Proses Data

1. **Data Collection**: Command `SyncCovidData` mengambil data dari COVID API
2. **Data Storage**: Data disimpan ke database melalui model `CovidStat`
3. **Data Processing**: Livewire component memproses data untuk dashboard
4. **Data Visualization**: Chart.js menampilkan timeline dalam bentuk line chart
5. **Real-time Updates**: Livewire polling setiap 15 detik untuk update otomatis

## File-File Utama

### Console Command
```php
// app/Console/Commands/SyncCovidData.php
- Mengambil data dari COVID API
- Menyimpan ke database dengan model CovidStat
- Menggunakan HttpService untuk HTTP requests
```

### Model
```php
// app/Models/CovidStat.php
- Eloquent model untuk tabel covid_stats
```

### Livewire Component
```php
// resources/views/livewire/dashboard/index.blade.php
- Volt component untuk dashboard
- Real-time data loading
- Chart data preparation
```

### Blade Components
```php
// resources/views/components/scoreboard-card.blade.php
- Reusable UI component
- Props untuk data dan styling
```

## Sinkronisasi Data

### Manual Sync
```bash
# Menjalankan sync secara manual
php artisan app:sync-covid-data
```

### Automated Sync (Scheduled)
```php
// routes/console.php
Schedule::command('app:sync-covid-data')
    ->dailyAt('00:00')
    ->onFailure(function () {
        Log::error('Sinkronisasi data COVID-19 gagal.');
    })
    ->withoutOverlapping()
    ->runInBackground();
```

### Setup Cron Job
Untuk mengaktifkan scheduled command, tambahkan cron job berikut:

```bash
# Edit crontab
crontab -e

# Tambahkan baris berikut (ganti /path-to-your-project dengan path aplikasi)
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

Data diambil dari 30 hari terakhir (3 tahun yang lalu untuk simulasi) dengan endpoint:
- Indonesia: `covid-api.com/api/reports/total?date={date}&iso=IDN`
- Global: `covid-api.com/api/reports/total?date={date}`

## Struktur Database

### Table: `covid_stats`
```sql
- id (primary key)
- date (date) - Tanggal data
- region_name (string) - Nama wilayah (Indonesia/Global)
- region_iso (string, nullable) - Kode ISO negara
- confirmed (integer) - Kasus terkonfirmasi
- deaths (integer) - Kasus meninggal
- recovered (integer) - Kasus sembuh
- created_at, updated_at (timestamps)

# Index
- Unique: [date, region_name]
- Index: [date, region_iso]
```

**Alasan Desain**:
- **Denormalisasi**: Menyimpan `region_name` dan `region_iso` untuk performa query
- **Unique Constraint**: Mencegah duplikasi data per tanggal dan region
- **Nullable ISO**: Global data tidak memiliki kode ISO
- **Integer Type**: Untuk kasus angka, lebih efisien daripada string

## Konfigurasi Laravel 11

### HTTP Configuration
```php
// config/services.php
'http' => [
    'verify' => env('HTTP_VERIFY_SSL', true),
    'timeout' => env('HTTP_TIMEOUT', 30),
    'connect_timeout' => env('HTTP_CONNECT_TIMEOUT', 10),
],
```

### Asset Compilation
```javascript
// vite.config.js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
});
```

## Tantangan Teknis & Solusi


### 1. Chart Data Performance
**Masalah**: Query database berulang untuk chart
```php
// Solusi: Single query dengan collection filtering
$timeline = CovidStat::where('date', '>=', $startDate)->get();
$chartData = [
    'labels' => $timeline->where('region_name', 'Global')->pluck('date'),
    'indonesia' => $timeline->where('region_name', 'Indonesia')->pluck('confirmed'),
    'global' => $timeline->where('region_name', 'Global')->pluck('confirmed'),
];
```

### 2. Component Reusability
**Masalah**: UI component yang dapat digunakan ulang
```php
// Solusi: Blade component di resources/views/components/
<x-scoreboard-card title="Indonesia" :dataScore="$dataIndonesia">
    <!-- Slot content -->
</x-scoreboard-card>
```

### 3. Scheduled Command Setup
**Masalah**: Otomatisasi sync data harian
```php
// Solusi: Konfigurasi di routes/console.php dengan error handling
Schedule::command('app:sync-covid-data')
    ->dailyAt('00:00')
    ->onFailure(function () {
        Log::error('Sinkronisasi data COVID-19 gagal.');
    })
    ->withoutOverlapping()
    ->runInBackground();
```

## Instalasi

```bash
# Clone dan setup
git clone <repo-url>
cd laravel-headless-cms
composer install
npm install

# Environment
cp .env.example .env
php artisan key:generate

# Database
php artisan migrate
php artisan app:sync-covid-data

# Assets
npm run build

# Development server
php artisan serve
```

## Environment Configuration

```env
# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=covid_dashboard
DB_USERNAME=root
DB_PASSWORD=

# HTTP Settings
HTTP_VERIFY_SSL=false
HTTP_TIMEOUT=30
HTTP_CONNECT_TIMEOUT=10

# Application
APP_NAME="COVID-19 Dashboard"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost
```

## Testing

```bash
# Run tests
php artisan test

# Test command
php artisan app:sync-covid-data

# Test schedule
php artisan schedule:list
php artisan schedule:run
```