<?php

namespace App\Services\Scanner;

use Illuminate\Support\Facades\Log;

class ScannerEngine
{
    public function __construct(
        private ScannerState $state,
        private ScannerStockRepository $stocks,
        private CandidateDetector $detector,
        private CandidateRepository $candidates,
    ) {}

    public function processMinute(array $events): void
    {
        foreach ($events as $event) {
            $history = $this->stocks->get($event['sym']);
            // dd($history);

            if (!$history) {
                continue;
            }

            if (
                !$history->rth_close ||
                !$history->adv20 ||
                !$history->ah_close
            ) {
                continue;
            }

            $state = $this->state->get($event['sym']);

            if ($state->todayPmOpen === null) {
                $state->todayPmOpen = $event['o'];
            }


            /*
             * Update live values
             */
            $state->price = $event['c'];
            $state->high = $event['h'];
            $state->low = $event['l'];

            $state->minuteVolume = $event['v'];
            $state->accumulatedVolume = $event['av'];

            $state->vwap = $event['vw'];
            $state->transactions = $event['z'];

            $state->lastMinute = date('H:i', $event['s'] / 1000);

            /*
             * Temporary calculations
             */
            $gap = null;

            if ($history->rth_close && $history->rth_close > 0) {
                $gap = (($state->todayPmOpen - $history->rth_close) / $history->rth_close) * 100;
            }

            $rvol = null;

            if ($history->adv20 && $history->adv20 > 0) {
                $rvol = $state->accumulatedVolume / $history->adv20;
            }

            $priceChange = null;

            if ($state->todayPmOpen && $history->pm_open > 0) {
                $priceChange = (($state->price - $state->todayPmOpen) / $state->todayPmOpen) * 100;
            }

            $afterHourChange = null;

            if($history->ah_close) {
                $afterHourChange = (($history->ah_close - $history->rth_close) / $history->rth_close) * 100;
            }

            $candidate = $this->detector->detect(

                $history,

                $state,

                $gap,

                $priceChange,

                $rvol,

                $afterHourChange,

            );

            if ($candidate) {
                $this->candidates->upsert($candidate);
            } else {
                $this->candidates->remove($event['sym']);
            }
        }
    }
}
