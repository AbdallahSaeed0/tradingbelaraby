<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Analytics\Format;
use App\Services\Analytics\Period;
use App\Services\Analytics\ReportCatalog;
use App\Services\GoogleAnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

/** Website traffic (Google Analytics 4) inside the admin panel. */
class SiteAnalyticsController extends Controller
{
    /** Max rows in a CSV export. */
    private const EXPORT_LIMIT = 10000;

    public function index(Request $request, GoogleAnalyticsService $analytics)
    {
        $days = (int) $request->query('days', 28);
        if (!in_array($days, GoogleAnalyticsService::PERIODS, true)) {
            $days = 28;
        }

        $problem = $analytics->configurationProblem();
        $report = null;
        $realtime = null;
        $error = null;

        if ($problem === null) {
            try {
                $report = $analytics->overview($days, $request->boolean('refresh'));
                $realtime = $analytics->realtime();
            } catch (\Throwable $e) {
                Log::error('Google Analytics report failed: ' . $e->getMessage());
                $error = $e->getMessage();
            }
        }

        return view('admin.site-analytics.index', [
            'days' => $days,
            'periods' => GoogleAnalyticsService::PERIODS,
            'problem' => $problem,
            'error' => $error,
            'report' => $report,
            'realtime' => $realtime,
            'reports' => ReportCatalog::all(),
        ]);
    }

    /** A full, sortable, searchable report table (pages, sources, locations...). */
    public function report(Request $request, string $reportKey, GoogleAnalyticsService $analytics)
    {
        $definition = ReportCatalog::find($reportKey) ?? abort(404);
        $viewKey = $this->viewKey($request, $definition);
        $view = $definition['views'][$viewKey];
        $period = Period::fromRequest($request);

        $sortable = ReportCatalog::sortableMetrics($definition);
        $sort = in_array($request->query('sort'), $sortable, true) ? $request->query('sort') : null;
        $desc = $request->query('dir') !== 'asc';
        $search = mb_substr(trim((string) $request->query('q', '')), 0, 100);
        $page = max(1, (int) $request->query('page', 1));

        $problem = $analytics->configurationProblem();
        $table = null;
        $error = null;

        if ($problem === null) {
            try {
                if ($request->query('export') === 'csv') {
                    return $this->exportCsv($analytics, $reportKey, $definition, $viewKey, $period, $sort, $desc, $search);
                }
                $table = $analytics->table($definition, $viewKey, $period, [
                    'sort' => $sort,
                    'desc' => $desc,
                    'search' => $search,
                    'page' => $page,
                    'fresh' => $request->boolean('refresh'),
                ]);
            } catch (\Throwable $e) {
                Log::error("Google Analytics report [{$reportKey}] failed: " . $e->getMessage());
                $error = $e->getMessage();
            }
        }

        $paginator = null;
        if ($table) {
            $perPage = (int) ($view['per_page'] ?? 25);
            $paginator = (new LengthAwarePaginator($table['rows'], $table['row_count'], $perPage, $page, [
                'path' => $request->url(),
                'query' => $request->except(['page', 'refresh']),
            ]))->onEachSide(1);
        }

        return view('admin.site-analytics.report', [
            'reports' => ReportCatalog::all(),
            'reportKey' => $reportKey,
            'definition' => $definition,
            'viewKey' => $viewKey,
            'view' => $view,
            'period' => $period,
            'sortable' => $sortable,
            // Shown as the active sort column; time views are ordered by hour/day instead.
            'sort' => $sort ?? (!empty($view['sort_dimension']) ? null : $sortable[0]),
            'desc' => $desc,
            'search' => $search,
            'problem' => $problem,
            'error' => $error,
            'table' => $table,
            'paginator' => $paginator,
        ]);
    }

    /** Drill-down page for one row of a report (one page, one country, one source...). */
    public function item(Request $request, string $reportKey, GoogleAnalyticsService $analytics)
    {
        $definition = ReportCatalog::find($reportKey) ?? abort(404);
        $viewKey = $this->viewKey($request, $definition);
        $view = $definition['views'][$viewKey];
        $period = Period::fromRequest($request);

        // Only accept filters on this view's own dimensions.
        $allowed = array_column($view['dimensions'], 'name');
        $match = [];
        foreach ((array) $request->query('f', []) as $name => $value) {
            if (in_array($name, $allowed, true) && is_string($value) && $value !== '') {
                $match[$name] = mb_substr($value, 0, 500);
            }
        }
        if (!$match) {
            return redirect()->route('admin.site-analytics.report', ['reportKey' => $reportKey, 'view' => $viewKey] + $period->query());
        }

        $problem = $analytics->configurationProblem();
        $item = null;
        $error = null;

        if ($problem === null) {
            try {
                $item = $analytics->item($definition, $match, $period, $request->boolean('refresh'));
            } catch (\Throwable $e) {
                Log::error("Google Analytics item report [{$reportKey}] failed: " . $e->getMessage());
                $error = $e->getMessage();
            }
        }

        return view('admin.site-analytics.item', [
            'reports' => ReportCatalog::all(),
            'reportKey' => $reportKey,
            'definition' => $definition,
            'viewKey' => $viewKey,
            'view' => $view,
            'period' => $period,
            'match' => $match,
            'problem' => $problem,
            'error' => $error,
            'item' => $item,
        ]);
    }

    /** JSON for the auto-refreshing "users right now" card. */
    public function realtime(GoogleAnalyticsService $analytics)
    {
        if (!$analytics->isConfigured()) {
            return response()->json(['message' => $analytics->configurationProblem()], 503);
        }

        try {
            return response()->json($analytics->realtime());
        } catch (\Throwable $e) {
            Log::error('Google Analytics realtime report failed: ' . $e->getMessage());

            return response()->json(['message' => 'Could not load realtime data.'], 502);
        }
    }

    private function viewKey(Request $request, array $definition): string
    {
        $viewKey = (string) $request->query('view', '');

        return array_key_exists($viewKey, $definition['views']) ? $viewKey : array_key_first($definition['views']);
    }

    private function exportCsv(GoogleAnalyticsService $analytics, string $reportKey, array $definition, string $viewKey, Period $period, ?string $sort, bool $desc, string $search)
    {
        $view = $definition['views'][$viewKey];
        $table = $analytics->table($definition, $viewKey, $period, [
            'sort' => $sort,
            'desc' => $desc,
            'search' => $search,
            'limit' => self::EXPORT_LIMIT,
            'offset' => 0,
        ]);
        $dimensions = array_values(array_filter($view['dimensions'], fn ($d) => empty($d['hidden'])));
        $filename = "analytics-{$reportKey}-{$viewKey}-{$period->startDate()}-to-{$period->endDate()}.csv";

        return response()->streamDownload(function () use ($table, $dimensions, $definition) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // UTF-8 BOM so Excel shows Arabic correctly
            fputcsv($out, array_merge(array_column($dimensions, 'label'), array_column($definition['metrics'], 'label')));
            foreach ($table['rows'] as $row) {
                $line = [];
                foreach ($dimensions as $d) {
                    $value = Format::dimension($row['dimensions'][$d['name']] ?? '', $d['format'] ?? null);
                    // Campaign/source names come from visitors' links; stop Excel from running them as formulas.
                    $line[] = preg_match('/^[=+\-@\t\r]/', $value) ? "'" . $value : $value;
                }
                foreach ($definition['metrics'] as $m) {
                    $value = $row['metrics'][$m['key']];
                    $line[] = match ($m['format']) {
                        'percent' => round($value * 100, 2),
                        'duration', 'decimal' => round($value, 2),
                        default => (int) $value,
                    };
                }
                fputcsv($out, $line);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
