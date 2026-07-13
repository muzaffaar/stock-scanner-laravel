<?php
namespace App\Services\Polygon;

use App\Services\Polygon\PolygonClient;
use Carbon\Carbon;

class NewsService
{
    public function __construct(
        private PolygonClient $polygon,
    ) {
    }

    public function latestForTicker(string $ticker): ?array
    {
        $articles = $this->polygon->news([
            'ticker' => $ticker,
            'limit' => 5,
            'sort' => 'published_utc',
            'order' => 'desc',
        ]);

        if (empty($articles)) {
            return null;
        }

        dump($articles[0]);

        foreach ($articles as $article) {

            if (! in_array($ticker, $article['tickers'] ?? [])) {
                continue;
            }

            return $article;
        }

        return null;
    }
}
