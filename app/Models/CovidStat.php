<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CovidStat extends Model
{
    protected $fillable = [
        'region_name',
        'region_iso',
        'date',
        'confirmed',
        'deaths',
        'recovered',
    ];

    protected $casts = [
        'date' => 'date',
        'confirmed' => 'integer',
        'deaths' => 'integer',
        'recovered' => 'integer',
    ];
}
