<?php

namespace App\Services\Scanner;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Generator;

class MinuteReaderService
{
    public function stream(Carbon $date): Generator
    {
        $partition = 'minute_aggregates_' . $date->format('Ymd');

        $rows = DB::connection('scanner')
            ->table($partition)
            ->orderBy('minute')
            ->orderBy('ticker')
            ->cursor();

        $currentMinute = null;

        $batch = [];

        foreach ($rows as $row) {

            if ($currentMinute === null) {

                $currentMinute = $row->minute;

            }

            if ($row->minute != $currentMinute) {

                yield [
                    'minute' => $currentMinute,
                    'rows' => $batch,
                ];

                $batch = [];

                $currentMinute = $row->minute;

            }

            $batch[] = [
                'ev' => 'AM',

                'sym' => $row->ticker,

                'o' => (float) $row->open,
                'c' => (float) $row->close,
                'h' => (float) $row->high,
                'l' => (float) $row->low,

                // minute volume
                'v' => (int) $row->volume,

                // accumulated day volume
                'av' => (int) $row->accumulated_volume,

                // VWAP
                'vw' => (float) $row->vwap,

                // number of trades
                'z' => (int) $row->transactions,

                // minute start/end
                's' => Carbon::parse($row->minute)->timestamp * 1000,
                'e' => Carbon::parse($row->minute)
                        ->addMinute()
                        ->subMillisecond()
                        ->timestamp * 1000,
            ];
        }

        if (!empty($batch)) {

            yield [

                'minute' => $currentMinute,

                'rows' => $batch,

            ];

        }
    }
}
