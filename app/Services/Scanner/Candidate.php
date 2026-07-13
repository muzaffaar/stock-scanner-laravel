<?php

namespace App\Services\Scanner;

class Candidate
{
    public function __construct(
        public string $ticker,
        public string $minute,

        public float $price,

        public float $gap,

        public float $priceChange,

        public float $afterHourChange,

        public float $rvol,

        public ?int $float = null,
    ) {}
}
