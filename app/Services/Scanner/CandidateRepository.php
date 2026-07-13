<?php

namespace App\Services\Scanner;

use App\Jobs\FetchCandidateNewsJob;
use App\Models\Candidate;

class CandidateRepository
{
    public function upsert(\App\Services\Scanner\Candidate $candidate): void
    {
        $model = Candidate::updateOrCreate(
            [
                'ticker' => $candidate->ticker,
            ],
            [
                'minute' => $candidate->minute,
                'price' => $candidate->price,
                'gap' => $candidate->gap,
                'price_change' => $candidate->priceChange,
                'ah_change' => $candidate->afterHourChange,
                'rvol' => $candidate->rvol,
                'float' => $candidate->float,
                'updated_at' => now(),
            ]
        );

        if ($model->wasRecentlyCreated) {
            FetchCandidateNewsJob::dispatch($candidate->ticker);
        }
    }

    public function remove(string $ticker): void
    {
        Candidate::where('ticker', $ticker)->delete();
    }

    public function all()
    {
        return Candidate::orderByDesc('price_change')->get();
    }

    public function clear(): void
    {
        Candidate::truncate();
    }
}
