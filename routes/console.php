<?php

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;


// 
Schedule::command('app:sync-covid-data')
    ->dailyAt('00:00')
    ->onFailure(function () {
        Log::error('Sinkronisasi data COVID-19 gagal.');
    })
    ->withoutOverlapping()
    ->runInBackground();
