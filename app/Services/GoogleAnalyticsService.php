<?php

namespace App\Services;

use Carbon\Carbon;
use Google\Analytics\Data\V1beta\Client\BetaAnalyticsDataClient;
use Google\Analytics\Data\V1beta\DateRange;
use Google\Analytics\Data\V1beta\Dimension;
use Google\Analytics\Data\V1beta\Metric;
use Google\Analytics\Data\V1beta\OrderBy;
use Google\Analytics\Data\V1beta\OrderBy\MetricOrderBy;
use Google\Analytics\Data\V1beta\RunRealtimeReportRequest;
use Google\Analytics\Data\V1beta\RunReportRequest;
use Illuminate\Support\Facades\Cache;

/**
 * Reads GA4 reports through the Google Analytics Data API using a service account.
 * Results are cached so the admin page stays fast and well inside the API quotas.
 */
class GoogleAnalyticsService
{
    public const PERIODS = [7, 28, 90];

    private ?BetaAnalyticsDataClient $client = null;

    public function isConfigured(): bool
    {
        return $this->propertyId() !== null && is_file($this->credentialsPath());
    }

    /** Human-readable reason the integration is not usable yet, or null when it is configured. */
    public function configurationProblem(): ?string
    {
        if ($this->propertyId() === null) {
            return 'ANALYTICS_PROPERTY_ID is not set in .env.';
        }
        if (!is_file($this->credentialsPath())) {
            return 'Service account key not found at ' . $this->credentialsPath() . '.';
        }

        return null;
    }

    /** Everything the analytics page shows for the last $days days (including today). */
    public function overview(int $days, bool $fresh = false): array
    {
        $days = in_array($days, self::PERIODS, true) ? $days : 28;
        $key = "ga4:overview:{$this->propertyId()}:{$days}";

        if ($fresh) {
            Cache::forget($key);
        }

        return Cache::remember($key, now()->addMinutes(max(1, (int) config('services.google_analytics.cache_minutes', 60))), fn () => [
            'days' => $days,
            'totals' => $this->totals($days),
            'daily' => $this->daily($days),
            'top_pages' => $this->topPages($days),
            'channels' => $this->breakdown($days, 'sessionDefaultChannelGroup', 'sessions', 8),
            'countries' => $this->breakdown($days, 'country', 'activeUsers', 8),
            'devices' => $this->breakdown($days, 'deviceCategory', 'activeUsers', 5),
            'fetched_at' => now()->toIso8601String(),
        ]);
    }

    /** Users active in the last 30 minutes, and the pages they are on. */
    public function realtime(): array
    {
        return Cache::remember("ga4:realtime:{$this->propertyId()}", now()->addMinute(), function () {
            $totalResponse = $this->client()->runRealtimeReport(
                (new RunRealtimeReportRequest())
                    ->setProperty($this->property())
                    ->setMetrics([new Metric(['name' => 'activeUsers'])])
            );
            $activeUsers = 0;
            foreach ($totalResponse->getRows() as $row) {
                $activeUsers += (int) $row->getMetricValues()[0]->getValue();
            }

            $pagesResponse = $this->client()->runRealtimeReport(
                (new RunRealtimeReportRequest())
                    ->setProperty($this->property())
                    ->setDimensions([new Dimension(['name' => 'unifiedScreenName'])])
                    ->setMetrics([new Metric(['name' => 'screenPageViews']), new Metric(['name' => 'activeUsers'])])
                    ->setOrderBys([$this->orderByMetric('screenPageViews')])
                    ->setLimit(10)
            );
            $pages = [];
            foreach ($pagesResponse->getRows() as $row) {
                $pages[] = [
                    'title' => $row->getDimensionValues()[0]->getValue(),
                    'views' => (int) $row->getMetricValues()[0]->getValue(),
                    'users' => (int) $row->getMetricValues()[1]->getValue(),
                ];
            }

            return [
                'active_users' => $activeUsers,
                'pages' => $pages,
                'fetched_at' => now()->toIso8601String(),
            ];
        });
    }

    /** Headline metrics for the period and the period before it, with % change. */
    private function totals(int $days): array
    {
        $metrics = ['activeUsers', 'newUsers', 'sessions', 'screenPageViews', 'averageSessionDuration', 'engagementRate'];

        $response = $this->client()->runReport(
            (new RunReportRequest())
                ->setProperty($this->property())
                ->setDateRanges([
                    new DateRange(['start_date' => ($days - 1) . 'daysAgo', 'end_date' => 'today', 'name' => 'current']),
                    new DateRange(['start_date' => (2 * $days - 1) . 'daysAgo', 'end_date' => $days . 'daysAgo', 'name' => 'previous']),
                ])
                ->setMetrics(array_map(fn ($m) => new Metric(['name' => $m]), $metrics))
        );

        $values = ['current' => array_fill_keys($metrics, 0.0), 'previous' => array_fill_keys($metrics, 0.0)];
        foreach ($response->getRows() as $row) {
            // With several date ranges the API adds the range name as the (only) dimension.
            $range = $row->getDimensionValues()[0]->getValue();
            if (!isset($values[$range])) {
                continue;
            }
            foreach ($metrics as $i => $metric) {
                $values[$range][$metric] = (float) $row->getMetricValues()[$i]->getValue();
            }
        }

        $totals = [];
        foreach ($metrics as $metric) {
            $current = $values['current'][$metric];
            $previous = $values['previous'][$metric];
            $totals[$metric] = [
                'value' => $current,
                'previous' => $previous,
                'change' => $previous > 0 ? round(($current - $previous) / $previous * 100, 1) : null,
            ];
        }

        return $totals;
    }

    /** Users and page views per day, with days that had no traffic filled in as zero. */
    private function daily(int $days): array
    {
        $response = $this->client()->runReport(
            (new RunReportRequest())
                ->setProperty($this->property())
                ->setDateRanges([new DateRange(['start_date' => ($days - 1) . 'daysAgo', 'end_date' => 'today'])])
                ->setDimensions([new Dimension(['name' => 'date'])])
                ->setMetrics([new Metric(['name' => 'activeUsers']), new Metric(['name' => 'screenPageViews'])])
                ->setLimit(400)
        );

        $byDate = [];
        foreach ($response->getRows() as $row) {
            $byDate[$row->getDimensionValues()[0]->getValue()] = [
                (int) $row->getMetricValues()[0]->getValue(),
                (int) $row->getMetricValues()[1]->getValue(),
            ];
        }

        $series = ['labels' => [], 'users' => [], 'views' => []];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            [$users, $views] = $byDate[$date->format('Ymd')] ?? [0, 0];
            $series['labels'][] = $date->format('M d');
            $series['users'][] = $users;
            $series['views'][] = $views;
        }

        return $series;
    }

    private function topPages(int $days): array
    {
        $response = $this->client()->runReport(
            (new RunReportRequest())
                ->setProperty($this->property())
                ->setDateRanges([new DateRange(['start_date' => ($days - 1) . 'daysAgo', 'end_date' => 'today'])])
                ->setDimensions([new Dimension(['name' => 'pagePath']), new Dimension(['name' => 'pageTitle'])])
                ->setMetrics([
                    new Metric(['name' => 'screenPageViews']),
                    new Metric(['name' => 'activeUsers']),
                    new Metric(['name' => 'userEngagementDuration']),
                ])
                ->setOrderBys([$this->orderByMetric('screenPageViews')])
                ->setLimit(15)
        );

        $pages = [];
        foreach ($response->getRows() as $row) {
            $users = (int) $row->getMetricValues()[1]->getValue();
            $pages[] = [
                'path' => $row->getDimensionValues()[0]->getValue(),
                'title' => $row->getDimensionValues()[1]->getValue(),
                'views' => (int) $row->getMetricValues()[0]->getValue(),
                'users' => $users,
                // Average engagement time per user, in seconds.
                'avg_time' => $users > 0 ? (int) round((float) $row->getMetricValues()[2]->getValue() / $users) : 0,
            ];
        }

        return $pages;
    }

    /** Top values of one dimension ranked by one metric, e.g. sessions per traffic channel. */
    private function breakdown(int $days, string $dimension, string $metric, int $limit): array
    {
        $response = $this->client()->runReport(
            (new RunReportRequest())
                ->setProperty($this->property())
                ->setDateRanges([new DateRange(['start_date' => ($days - 1) . 'daysAgo', 'end_date' => 'today'])])
                ->setDimensions([new Dimension(['name' => $dimension])])
                ->setMetrics([new Metric(['name' => $metric])])
                ->setOrderBys([$this->orderByMetric($metric)])
                ->setLimit($limit)
        );

        $rows = [];
        foreach ($response->getRows() as $row) {
            $rows[] = [
                'label' => $row->getDimensionValues()[0]->getValue(),
                'value' => (int) $row->getMetricValues()[0]->getValue(),
            ];
        }

        return $rows;
    }

    private function orderByMetric(string $metric): OrderBy
    {
        return new OrderBy(['metric' => new MetricOrderBy(['metric_name' => $metric]), 'desc' => true]);
    }

    private function client(): BetaAnalyticsDataClient
    {
        // REST transport, so the server does not need the grpc PHP extension.
        return $this->client ??= new BetaAnalyticsDataClient([
            'credentials' => $this->credentialsPath(),
            'transport' => 'rest',
        ]);
    }

    private function property(): string
    {
        return 'properties/' . $this->propertyId();
    }

    private function propertyId(): ?string
    {
        $id = trim((string) config('services.google_analytics.property_id'));

        return $id !== '' ? $id : null;
    }

    private function credentialsPath(): string
    {
        return (string) config('services.google_analytics.credentials_path');
    }
}
