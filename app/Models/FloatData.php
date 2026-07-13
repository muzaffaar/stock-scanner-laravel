<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FloatData extends Model
{
    protected $table = 'floats';

    protected $fillable = [
        'ticker',
        'float',
        'float_percent',
        'effective_date',
    ];

    protected $casts = [
        'effective_date' => 'date',
    ];
}
