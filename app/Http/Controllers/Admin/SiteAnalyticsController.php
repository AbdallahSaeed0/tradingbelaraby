<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\GoogleAnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/** Website traffic (Google Analytics 4) inside the admin panel. */
class SiteAnalyticsController extends Controller
{
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
}
