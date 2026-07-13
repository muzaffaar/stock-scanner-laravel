<?php

namespace App\Services\Scanner;

use App\Models\Stock;

class CandidateDetector
{
    public function detect(
        Stock $history,
        TickerState $state,
        float $gap,
        float $priceChange,
        float $rvol,
        float $afterHourChange,
    ): ?Candidate {

        /*
         * First simple rules.
         */

        if ($state->price < 1 || $state->price > 20) {
            return null;
        }

        if ($gap < 10) {
            return null;
        }

        if ($priceChange < 10) {
            return null;
        }

        return new Candidate(

            ticker: $history->ticker,

            minute: $state->lastMinute,

            price: $state->price,

            gap: round($gap,2),

            afterHourChange: $afterHourChange,

            priceChange: round($priceChange,2),

            rvol: round($rvol,2),

            float: $history->float,

        );
    }
}
