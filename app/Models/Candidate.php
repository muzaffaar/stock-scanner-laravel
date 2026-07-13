<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    protected $table = 'candidates';

    protected $primaryKey = 'ticker';

    public $incrementing = false;

    protected $keyType = 'string';

    public $fillable = [
        'ticker',
        'minute',
        'price',
        'gap',
        'price_change',
        'rvol',
        'float',
        'ah_change',
        'has_news',
        'news_sentiment',
        'news_published_at',
        'news_title',
        'news_url',
        'news_checked_at'
    ];
}
