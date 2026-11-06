<?php

namespace App\Services;

use App\Services\Analytics\Period;
use App\Services\Analytics\ReportCatalog;
use Carbon\Carbon;
use Google\Analytics\Data\V1beta\Client\BetaAnalyticsDataClient;
use Google\Analytics\Data\V1beta\DateRange;
use Google\Analytics\Data\V1beta\Dimension;
use Google\Analytics\Data\V1beta\Filter;
use Google\Analytics\Data\V1beta\Filter\StringFilter;
use Google\Analytics\Data\V1beta\Filter\StringFilter\MatchType;
use Google\Analytics\Data\V1beta\FilterExpression;
use Google\Analytics\Data\V1beta\FilterExpressionList;
use Google\Analytics\Data\V1beta\Metric;
use Google\Analytics\Data\V1beta\MetricAggregation;
use Google\Analytics\Data\V1beta\OrderBy;
use Google\Analytics\Data\V1beta\OrderBy\DimensionOrderBy;
use Google\Analytics\Data\V1beta\OrderBy\DimensionOrderBy\OrderType;
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
        // Bump the version when the report shape changes, so old cached reports are not reused.
        $key = "ga4:overview:v2:{$this->propertyId()}:{$days}";

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
            'operating_systems' => $this->breakdown($days, 'operatingSystemWithVersion', 'activeUsers', 10),
            'browsers' => $this->breakdown($days, 'browser', 'activeUsers', 8),
            'phone_models' => $this->phoneModels($days),
            'screen_resolutions' => $this->breakdown($days, 'screenResolution', 'activeUsers', 8),
            'fetched_at' => now()->toIso8601String(),
        ]);
    }

    /**
     * One page of a detailed report (see ReportCatalog), with totals for the whole period.
     *
     * $options: sort (metric key), desc (bool), search (string), page (1-based), per_page,
     *           fresh (bool, skip cache), limit/offset override (used by CSV export).
     */
    public function table(array $report, string $viewKey, Period $period, array $options = []): array
    {
        $view = $report['views'][$viewKey];
        $perPage = (int) ($options['per_page'] ?? $view['per_page'] ?? 25);
        $page = max(1, (int) ($options['page'] ?? 1));
        $limit = (int) ($options['limit'] ?? $perPage);
        $offset = (int) ($options['offset'] ?? ($page - 1) * $perPage);
        $sort = $options['sort'] ?? null;
        $desc = (bool) ($options['desc'] ?? true);
        $search = trim((string) ($options['search'] ?? ''));

        $key = 'ga4:table:' . md5(json_encode([$this->propertyId(), $report['title'], $viewKey, $period->query(), $limit, $offset, $sort, $desc, $search]));
        if (!empty($options['fresh'])) {
            Cache::forget($key);
        }

        return Cache::remember($key, $this->cacheTtl(), function () use ($report, $view, $period, $limit, $offset, $sort, $desc, $search) {
            $dimensions = array_map(fn ($d) => $d['name'], $view['dimensions']);
            $apiMetrics = ReportCatalog::apiMetrics($report);

            $filters = [];
            if (!empty($view['exclude_not_set'])) {
                $filters[] = new FilterExpression(['not_expression' => $this->exactFilter($view['exclude_not_set'], '(not set)')]);
            }
            if ($search !== '') {
                $searchable = array_values(array_filter($view['dimensions'], fn ($d) => empty($d['hidden'])));
                $filters[] = $this->anyOf(array_map(fn ($d) => new FilterExpression(['filter' => new Filter([
                    'field_name' => $d['name'],
                    'string_filter' => new StringFilter(['value' => $search, 'match_type' => MatchType::CONTAINS, 'case_sensitive' => false]),
                ])]), $searchable));
            }

            if ($sort !== null && in_array($sort, ReportCatalog::sortableMetrics($report), true)) {
                $orderBy = new OrderBy(['metric' => new MetricOrderBy(['metric_name' => $sort]), 'desc' => $desc]);
            } elseif (!empty($view['sort_dimension'])) {
                $orderBy = new OrderBy([
                    'dimension' => new DimensionOrderBy(['dimension_name' => $view['sort_dimension'], 'order_type' => OrderType::NUMERIC]),
                    'desc' => false,
                ]);
            } else {
                $orderBy = $this->orderByMetric(ReportCatalog::sortableMetrics($report)[0]);
            }

            $request = (new RunReportRequest())
                ->setProperty($this->property())
                ->setDateRanges([$this->dateRange($period)])
                ->setDimensions(array_map(fn ($n) => new Dimension(['name' => $n]), $dimensions))
                ->setMetrics(array_map(fn ($n) => new Metric(['name' => $n]), $apiMetrics))
                ->setMetricAggregations([MetricAggregation::TOTAL])
                ->setOrderBys([$orderBy])
                ->setLimit($limit)
                ->setOffset($offset);
            if ($filters) {
                $request->setDimensionFilter($this->allOf($filters));
            }

            $response = $this->client()->runReport($request);

            $rows = [];
            foreach ($response->getRows() as $row) {
                $dims = [];
                foreach ($dimensions as $i => $name) {
                    $dims[$name] = $row->getDimensionValues()[$i]->getValue();
                }
                $rows[] = ['dimensions' => $dims, 'metrics' => $this->metricValues($report, $apiMetrics, $row->getMetricValues())];
            }

            $totals = null;
            foreach ($response->getTotals() as $totalRow) {
                $totals = $this->metricValues($report, $apiMetrics, $totalRow->getMetricValues());
                break;
            }

            return [
                'rows' => $rows,
                'totals' => $totals,
                'row_count' => (int) $response->getRowCount(),
                'fetched_at' => now()->toIso8601String(),
            ];
        });
    }

    /**
     * Drill-down for one row of a report, e.g. one page or one country:
     * its totals, a day-by-day trend and how it splits by channel / country / device / page.
     *
     * @param array<string,string> $match dimension name => exact value
     */
    public function item(array $report, array $match, Period $period, bool $fresh = false): array
    {
        $key = 'ga4:item:' . md5(json_encode([$this->propertyId(), $report['title'], $match, $period->query()]));
        if ($fresh) {
            Cache::forget($key);
        }

        return Cache::remember($key, $this->cacheTtl(), function () use ($report, $match, $period) {
            $filter = $this->allOf(array_map(
                fn ($name, $value) => $this->exactFilter($name, $value),
                array_keys($match),
                array_values($match)
            ));
            $apiMetrics = ReportCatalog::apiMetrics($report);
            $mainMetric = ReportCatalog::sortableMetrics($report)[0];

            // Totals for the whole period.
            $response = $this->client()->runReport(
                (new RunReportRequest())
                    ->setProperty($this->property())
                    ->setDateRanges([$this->dateRange($period)])
                    ->setMetrics(array_map(fn ($n) => new Metric(['name' => $n]), $apiMetrics))
                    ->setDimensionFilter($filter)
            );
            $totals = null;
            foreach ($response->getRows() as $row) {
                $totals = $this->metricValues($report, $apiMetrics, $row->getMetricValues());
            }

            // Day by day, for the first two plain metrics.
            $trendMetrics = array_slice(ReportCatalog::sortableMetrics($report), 0, 2);
            $response = $this->client()->runReport(
                (new RunReportRequest())
                    ->setProperty($this->property())
                    ->setDateRanges([$this->dateRange($period)])
                    ->setDimensions([new Dimension(['name' => 'date'])])
                    ->setMetrics(array_map(fn ($n) => new Metric(['name' => $n]), $trendMetrics))
                    ->setDimensionFilter($filter)
                    ->setLimit(1000)
            );
            $byDate = [];
            foreach ($response->getRows() as $row) {
                foreach ($trendMetrics as $i => $name) {
                    $byDate[$row->getDimensionValues()[0]->getValue()][$name] = (float) $row->getMetricValues()[$i]->getValue();
                }
            }
            $trend = ['labels' => [], 'series' => array_fill_keys($trendMetrics, [])];
            for ($date = $period->start->copy(); $date->lte($period->end); $date->addDay()) {
                $trend['labels'][] = $date->format('M d');
                foreach ($trendMetrics as $name) {
                    $trend['series'][$name][] = $byDate[$date->format('Ymd')][$name] ?? 0;
                }
            }

            // Splits by other dimensions. One failing split should not break the whole page.
            $breakdowns = [];
            foreach ($report['breakdowns'] as $dimension) {
                if (array_key_exists($dimension, $match)) {
                    continue;
                }
                try {
                    $response = $this->client()->runReport(
                        (new RunReportRequest())
                            ->setProperty($this->property())
                            ->setDateRanges([$this->dateRange($period)])
                            ->setDimensions([new Dimension(['name' => $dimension])])
                            ->setMetrics([new Metric(['name' => $mainMetric])])
                            ->setDimensionFilter($filter)
                            ->setOrderBys([$this->orderByMetric($mainMetric)])
                            ->setLimit(10)
                    );
                    $rows = [];
                    foreach ($response->getRows() as $row) {
                        $rows[] = ['label' => $row->getDimensionValues()[0]->getValue(), 'value' => (float) $row->getMetricValues()[0]->getValue()];
                    }
                    $breakdowns[$dimension] = ['rows' => $rows, 'error' => null];
                } catch (\Throwable $e) {
                    $breakdowns[$dimension] = ['rows' => [], 'error' => $e->getMessage()];
                }
            }

            return [
                'totals' => $totals,
                'trend' => $trend,
                'main_metric' => $mainMetric,
                'breakdowns' => $breakdowns,
                'fetched_at' => now()->toIso8601String(),
            ];
        });
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

    /**
     * Phones and tablets by brand and model, e.g. "Samsung Galaxy S23".
     * Desktops have no brand/model ("(not set)"), so they are left out. Apple does not
     * report the iPhone model to browsers, so all iPhones show up as "Apple iPhone".
     */
    private function phoneModels(int $days, int $limit = 10): array
    {
        $response = $this->client()->runReport(
            (new RunReportRequest())
                ->setProperty($this->property())
                ->setDateRanges([new DateRange(['start_date' => ($days - 1) . 'daysAgo', 'end_date' => 'today'])])
                ->setDimensions([
                    new Dimension(['name' => 'mobileDeviceBranding']),
                    new Dimension(['name' => 'mobileDeviceMarketingName']),
                    new Dimension(['name' => 'mobileDeviceModel']),
                ])
                ->setMetrics([new Metric(['name' => 'activeUsers'])])
                ->setOrderBys([$this->orderByMetric('activeUsers')])
                ->setLimit(50)
        );

        $models = [];
        foreach ($response->getRows() as $row) {
            [$brand, $marketingName, $model] = array_map(
                fn ($v) => $v->getValue() === '(not set)' ? '' : $v->getValue(),
                iterator_to_array($row->getDimensionValues())
            );
            $name = $marketingName !== '' ? $marketingName : $model;
            if ($brand === '' && $name === '') {
                continue;
            }
            // Marketing names sometimes already start with the brand ("Apple iPhone").
            $label = $brand !== '' && !str_starts_with(strtolower($name), strtolower($brand))
                ? trim($brand . ' ' . $name)
                : ($name !== '' ? $name : $brand);

            $models[$label] = ($models[$label] ?? 0) + (int) $row->getMetricValues()[0]->getValue();
        }

        arsort($models);

        return array_map(
            fn ($label, $value) => ['label' => (string) $label, 'value' => $value],
            array_keys(array_slice($models, 0, $limit, true)),
            array_slice($models, 0, $limit, true)
        );
    }

    /** Column values for one row, computing ratio metrics (e.g. avg. time = engagement time / users). */
    private function metricValues(array $report, array $apiMetrics, $values): array
    {
        $raw = [];
        foreach ($apiMetrics as $i => $name) {
            $raw[$name] = (float) $values[$i]->getValue();
        }

        $result = [];
        foreach ($report['metrics'] as $metric) {
            if (isset($metric['ratio'])) {
                [$numerator, $denominator] = $metric['ratio'];
                $result[$metric['key']] = $raw[$denominator] > 0 ? $raw[$numerator] / $raw[$denominator] : 0.0;
            } else {
                $result[$metric['key']] = $raw[$metric['key']];
            }
        }

        return $result;
    }

    private function dateRange(Period $period): DateRange
    {
        return new DateRange(['start_date' => $period->startDate(), 'end_date' => $period->endDate()]);
    }

    private function exactFilter(string $dimension, string $value): FilterExpression
    {
        return new FilterExpression(['filter' => new Filter([
            'field_name' => $dimension,
            'string_filter' => new StringFilter(['value' => $value, 'match_type' => MatchType::EXACT, 'case_sensitive' => true]),
        ])]);
    }

    /** @param FilterExpression[] $expressions */
    private function allOf(array $expressions): FilterExpression
    {
        return count($expressions) === 1
            ? $expressions[0]
            : new FilterExpression(['and_group' => new FilterExpressionList(['expressions' => $expressions])]);
    }

    /** @param FilterExpression[] $expressions */
    private function anyOf(array $expressions): FilterExpression
    {
        return count($expressions) === 1
            ? $expressions[0]
            : new FilterExpression(['or_group' => new FilterExpressionList(['expressions' => $expressions])]);
    }

    private function cacheTtl(): \DateTimeInterface
    {
        return now()->addMinutes(max(1, (int) config('services.google_analytics.cache_minutes', 60)));
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
