<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $table = 'stocks';
    protected $fillable = [
        'ticker',
        'trading_date',

        // Prices
        'pm_open',
        'rth_open',
        'high',
        'low',
        'rth_close',
        'ah_close',

        // Volume
        'volume',
        'adv20',
        'adv50',
        'adv90',

        // Float
        'float',
        'float_percent',
        'transactions'
    ];

    protected $casts = [
        'trading_date' => 'date',

        'pm_open'       => 'decimal:4',
        'rth_open'      => 'decimal:4',
        'high'          => 'decimal:4',
        'low'           => 'decimal:4',
        'rth_close'     => 'decimal:4',
        'ah_close'       => 'decimal:4',

        'volume'        => 'integer',
        'avg_volume_20' => 'integer',
        'float'         => 'integer',
        'float_percent' => 'decimal:2',
    ];
}
