<?php

namespace App\Services\Analytics;

/**
 * Definitions of the detailed GA4 reports in the admin panel.
 *
 * Each report has:
 *  - metrics:    columns shown for every row. 'ratio' metrics are computed from two API metrics
 *                (e.g. average time = engagement time / users) and cannot be sorted by.
 *  - views:      ways to group the rows ("group by" tabs), each with its own dimensions.
 *                Hidden dimensions are fetched (for sorting/filtering) but not shown.
 *  - breakdowns: dimensions used on the drill-down page of a single row.
 *
 * Only dimension/metric combinations that GA4 allows together are used here.
 */
class ReportCatalog
{
    private const STANDARD_METRICS = [
        ['key' => 'activeUsers', 'label' => 'Visitors', 'format' => 'number'],
        ['key' => 'newUsers', 'label' => 'New visitors', 'format' => 'number'],
        ['key' => 'sessions', 'label' => 'Sessions', 'format' => 'number'],
        ['key' => 'screenPageViews', 'label' => 'Page views', 'format' => 'number'],
        ['key' => 'engagementRate', 'label' => 'Engagement rate', 'format' => 'percent'],
        ['key' => 'averageSessionDuration', 'label' => 'Avg. session time', 'format' => 'duration'],
    ];

    public static function all(): array
    {
        return [
            'pages' => [
                'title' => 'Pages',
                'icon' => 'fa-file-alt',
                'description' => 'Every page of the site and how much it is viewed.',
                'metrics' => [
                    ['key' => 'screenPageViews', 'label' => 'Views', 'format' => 'number'],
                    ['key' => 'activeUsers', 'label' => 'Visitors', 'format' => 'number'],
                    ['key' => 'screenPageViewsPerUser', 'label' => 'Views per visitor', 'format' => 'decimal'],
                    ['key' => 'avgEngagementTime', 'label' => 'Avg. time on page', 'format' => 'duration',
                        'ratio' => ['userEngagementDuration', 'activeUsers']],
                    ['key' => 'eventCount', 'label' => 'Events', 'format' => 'number'],
                ],
                'views' => [
                    'path' => ['label' => 'Page', 'dimensions' => [
                        ['name' => 'pagePath', 'label' => 'Page path', 'format' => 'path'],
                        ['name' => 'pageTitle', 'label' => 'Title'],
                    ]],
                    'title' => ['label' => 'Page title', 'dimensions' => [
                        ['name' => 'pageTitle', 'label' => 'Title'],
                    ]],
                ],
                'breakdowns' => ['sessionDefaultChannelGroup', 'sessionSource', 'country', 'deviceCategory'],
            ],

            'landing-pages' => [
                'title' => 'Landing pages',
                'icon' => 'fa-sign-in-alt',
                'description' => 'The first page visitors see when they arrive on the site.',
                'metrics' => [
                    ['key' => 'sessions', 'label' => 'Sessions', 'format' => 'number'],
                    ['key' => 'activeUsers', 'label' => 'Visitors', 'format' => 'number'],
                    ['key' => 'newUsers', 'label' => 'New visitors', 'format' => 'number'],
                    ['key' => 'engagementRate', 'label' => 'Engagement rate', 'format' => 'percent'],
                    ['key' => 'bounceRate', 'label' => 'Bounce rate', 'format' => 'percent'],
                    ['key' => 'averageSessionDuration', 'label' => 'Avg. session time', 'format' => 'duration'],
                ],
                'views' => [
                    'landing' => ['label' => 'Landing page', 'dimensions' => [
                        ['name' => 'landingPage', 'label' => 'Landing page', 'format' => 'path'],
                    ]],
                ],
                'breakdowns' => ['sessionDefaultChannelGroup', 'sessionSource', 'country', 'deviceCategory'],
            ],

            'sources' => [
                'title' => 'Traffic sources',
                'icon' => 'fa-route',
                'description' => 'Where visitors come from: Google, social media, other sites, campaigns.',
                'metrics' => self::STANDARD_METRICS,
                'views' => [
                    'channel' => ['label' => 'Channel', 'dimensions' => [
                        ['name' => 'sessionDefaultChannelGroup', 'label' => 'Channel'],
                    ]],
                    'source' => ['label' => 'Source / medium', 'dimensions' => [
                        ['name' => 'sessionSource', 'label' => 'Source'],
                        ['name' => 'sessionMedium', 'label' => 'Medium'],
                    ]],
                    'campaign' => ['label' => 'Campaign', 'dimensions' => [
                        ['name' => 'sessionCampaignName', 'label' => 'Campaign'],
                        ['name' => 'sessionSource', 'label' => 'Source'],
                    ]],
                ],
                'breakdowns' => ['pagePath', 'country', 'deviceCategory'],
            ],

            'locations' => [
                'title' => 'Locations',
                'icon' => 'fa-globe',
                'description' => 'Countries, cities and languages of your visitors.',
                'metrics' => self::STANDARD_METRICS,
                'views' => [
                    'country' => ['label' => 'Country', 'dimensions' => [
                        ['name' => 'country', 'label' => 'Country'],
                    ]],
                    'city' => ['label' => 'City', 'dimensions' => [
                        ['name' => 'city', 'label' => 'City'],
                        ['name' => 'country', 'label' => 'Country'],
                    ]],
                    'language' => ['label' => 'Language', 'dimensions' => [
                        ['name' => 'language', 'label' => 'Language'],
                    ]],
                ],
                'breakdowns' => ['pagePath', 'sessionDefaultChannelGroup', 'deviceCategory'],
            ],

            'technology' => [
                'title' => 'Devices & technology',
                'icon' => 'fa-mobile-alt',
                'description' => 'Device types, operating systems, browsers, phone models and screen sizes.',
                'metrics' => self::STANDARD_METRICS,
                'views' => [
                    'device' => ['label' => 'Device type', 'dimensions' => [
                        ['name' => 'deviceCategory', 'label' => 'Device', 'format' => 'capitalize'],
                    ]],
                    'os' => ['label' => 'Operating system', 'dimensions' => [
                        ['name' => 'operatingSystem', 'label' => 'Operating system'],
                    ]],
                    'os_version' => ['label' => 'OS version', 'dimensions' => [
                        ['name' => 'operatingSystemWithVersion', 'label' => 'OS version'],
                    ]],
                    'browser' => ['label' => 'Browser', 'dimensions' => [
                        ['name' => 'browser', 'label' => 'Browser'],
                    ]],
                    'model' => ['label' => 'Phone model', 'exclude_not_set' => 'mobileDeviceBranding', 'dimensions' => [
                        ['name' => 'mobileDeviceBranding', 'label' => 'Brand'],
                        ['name' => 'mobileDeviceMarketingName', 'label' => 'Model'],
                    ]],
                    'screen' => ['label' => 'Screen size', 'dimensions' => [
                        ['name' => 'screenResolution', 'label' => 'Screen resolution'],
                    ]],
                ],
                'breakdowns' => ['pagePath', 'sessionDefaultChannelGroup', 'country'],
            ],

            'time' => [
                'title' => 'Busiest times',
                'icon' => 'fa-clock',
                'description' => 'Which hours and days of the week your visitors are most active.',
                'metrics' => [
                    ['key' => 'activeUsers', 'label' => 'Visitors', 'format' => 'number'],
                    ['key' => 'sessions', 'label' => 'Sessions', 'format' => 'number'],
                    ['key' => 'screenPageViews', 'label' => 'Page views', 'format' => 'number'],
                    ['key' => 'engagementRate', 'label' => 'Engagement rate', 'format' => 'percent'],
                ],
                'views' => [
                    'hour' => ['label' => 'Hour of day', 'sort_dimension' => 'hour', 'per_page' => 24, 'dimensions' => [
                        ['name' => 'hour', 'label' => 'Hour', 'format' => 'hour'],
                    ]],
                    'weekday' => ['label' => 'Day of week', 'sort_dimension' => 'dayOfWeek', 'dimensions' => [
                        ['name' => 'dayOfWeek', 'label' => 'Day', 'hidden' => true],
                        ['name' => 'dayOfWeekName', 'label' => 'Day'],
                    ]],
                ],
                'breakdowns' => ['pagePath', 'sessionDefaultChannelGroup', 'deviceCategory'],
            ],

            'events' => [
                'title' => 'Events',
                'icon' => 'fa-bolt',
                'description' => 'Actions visitors take: page views, scrolls, link clicks, video plays, forms.',
                'metrics' => [
                    ['key' => 'eventCount', 'label' => 'Event count', 'format' => 'number'],
                    ['key' => 'totalUsers', 'label' => 'Users', 'format' => 'number'],
                    ['key' => 'eventCountPerUser', 'label' => 'Per user', 'format' => 'decimal'],
                ],
                'views' => [
                    'event' => ['label' => 'Event', 'dimensions' => [
                        ['name' => 'eventName', 'label' => 'Event name'],
                    ]],
                ],
                'breakdowns' => ['pagePath', 'country', 'deviceCategory'],
            ],
        ];
    }

    public static function find(string $key): ?array
    {
        return self::all()[$key] ?? null;
    }

    public static function keys(): array
    {
        return array_keys(self::all());
    }

    /** Labels and the report that lists each breakdown dimension (for "view all" links). */
    public static function breakdownInfo(string $dimension): array
    {
        return [
            'sessionDefaultChannelGroup' => ['label' => 'Channels', 'icon' => 'fa-route', 'report' => 'sources', 'view' => 'channel'],
            'sessionSource' => ['label' => 'Sources', 'icon' => 'fa-external-link-alt', 'report' => 'sources', 'view' => 'source'],
            'country' => ['label' => 'Countries', 'icon' => 'fa-globe', 'report' => 'locations', 'view' => 'country'],
            'deviceCategory' => ['label' => 'Devices', 'icon' => 'fa-mobile-alt', 'report' => 'technology', 'view' => 'device', 'format' => 'capitalize'],
            'pagePath' => ['label' => 'Pages', 'icon' => 'fa-file-alt', 'report' => 'pages', 'view' => 'path', 'format' => 'path'],
        ][$dimension] ?? ['label' => $dimension, 'icon' => 'fa-chart-bar', 'report' => null, 'view' => null];
    }

    /** Metrics that come straight from the API (not computed). */
    public static function sortableMetrics(array $report): array
    {
        return array_values(array_map(
            fn ($m) => $m['key'],
            array_filter($report['metrics'], fn ($m) => !isset($m['ratio']))
        ));
    }

    /** API metric names needed to show all columns, including the parts of computed ones. */
    public static function apiMetrics(array $report): array
    {
        $names = [];
        foreach ($report['metrics'] as $metric) {
            foreach ($metric['ratio'] ?? [$metric['key']] as $name) {
                $names[$name] = true;
            }
        }

        return array_keys($names);
    }
}
