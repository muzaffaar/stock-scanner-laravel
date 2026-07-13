<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\Candidate;
use App\Services\Polygon\NewsService;
use Illuminate\Support\Facades\DB;

class FetchCandidateNewsJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $ticker
    ){}

    /**
     * Execute the job.
     */
    public function handle(
        NewsService $newsService
    ): void {
        dump('JOB STARTED');
        logger('JOB STARTED');
        $candidate = Candidate::find($this->ticker);

        if (! $candidate) {
            logger()->warning("Candidate {$this->ticker} not found.");
            return;
        }

        if ($candidate->news_checked_at) {
            return;
        }

        $article = $newsService->latestForTicker($this->ticker);

        logger('NewsService called', [
            'ticker' => $this->ticker,
        ]);

        if (! $article) {

            $candidate->update([
                'news_checked_at' => now(),
            ]);

            return;
        }
        $candidate->update([
            'has_news' => true,
            'news_title' => $article['title'],
            'news_url' => $article['article_url'],
            'news_published_at' => $article['published_utc'],
            'news_sentiment' => data_get($article, 'insights.0.sentiment'),
            'news_checked_at' => now(),
        ]);
        logger()->info($candidate);
    }
}
