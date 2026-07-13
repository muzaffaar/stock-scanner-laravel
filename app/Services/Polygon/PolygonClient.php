<?php

namespace App\Services\Polygon;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class PolygonClient
{
    protected string $baseUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.polygon.base_url');
        $this->apiKey = config('services.polygon.api_key');
    }

    private function request(
        string $method,
        string $endpoint,
        array $query = []
    ): array {
        logger()->info('HTTP Request', [
            'url' => $this->baseUrl . $endpoint,
            'query' => $query,
        ]);
        $response = Http::timeout(30)
            ->retry(3, 1000)
            ->acceptJson()
            ->$method(
                $this->baseUrl . $endpoint,
                array_merge(
                    $query,
                    [
                        'apiKey' => $this->apiKey
                    ]
                )
            );

        $response->throw();
        logger()->info($response->json());

        return $response->json();
    }

    public function getTickers(?string $nextUrl = null): array
    {
        if ($nextUrl) {
            $separator = str_contains($nextUrl, '?') ? '&' : '?';

            return Http::get(
                $nextUrl . $separator . 'apiKey=' . $this->apiKey
            )->json();
        }

        return $this->request(
            'get',
            '/v3/reference/tickers',
            [
                'market' => 'stocks',
                'active' => 'true',
                'limit' => 1000
            ]
        );
    }

    public function tickerDetails(string $ticker): array
    {
        return $this->request(
            'get',
            "/v3/reference/tickers/{$ticker}"
        );
    }

    public function floats(?string $nextUrl = null): array
    {
        if ($nextUrl) {
            $separator = str_contains($nextUrl, '?') ? '&' : '?';

            return Http::get(
                $nextUrl . $separator . 'apiKey=' . $this->apiKey
            )->json();
        }

        return $this->request(
            'get',
            '/stocks/vX/float',
            [
                'limit' => 15000
            ]
        );
    }

    public function prevDayHistory(string $date)
    {
        $response = $this->request(
            'get',
            "/v2/aggs/grouped/locale/us/market/stocks/{$date}",
            [
                'adjusted' => 'true',
            ]
        );

        return $response['results'] ?? [];
    }

    public function news(array $filters = []): array
    {
        logger('PolygonClient::news()');
        $response = $this->request(
            'get',
            '/v2/reference/news',
            array_merge([
                'limit' => 5,
            ], $filters)
        );

        return $response['results'] ?? [];
    }

    public function marketStatus(): array
    {
        return $this->request(
            'get',
            '/v1/marketstatus/now'
        );
    }

    public function previousTradingDate(): string
    {
        $response = $this->request(
            'get',
            '/v2/aggs/grouped/locale/us/market/stocks/' . now()->toDateString(),
            [
                'adjusted' => 'true',
            ]
        );

        return Carbon::createFromTimestampMs(
            $response['queryCount'] ? $response['results'][0]['t'] : 0
        )->toDateString();
    }

    public function groupedDailyBars(string $date): array
    {
        return $this->request(
            'get',
            "/v2/aggs/grouped/locale/us/market/stocks/{$date}",
            [
                'adjusted' => 'true',
            ]
        );
    }

    public function getHistoricalVolumes(
        string $ticker,
        string $from,
        string $to
    ): array
    {
        $response = $this->request(
            'get',
            "/v2/aggs/ticker/{$ticker}/range/1/day/{$from}/{$to}"
        );

        return $response['results'] ?? [];
    }
}
