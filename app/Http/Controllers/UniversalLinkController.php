<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UniversalLinkController extends Controller
{
    /**
     * Apple App Site Association for Universal Links (no auth, no cache).
     */
    public function appleAppSiteAssociation(): JsonResponse
    {
        $data = [
            'applinks' => [
                'apps' => [],
                'details' => [
                    [
                        'appIDs' => ['96CT3492PR.com.education.coursesApp'],
                        'components' => [
                            [
                                '/' => '/app/*',
                                'comment' => 'Flutter app deep links',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        return response()->json($data, 200, [
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
        ], JSON_UNESCAPED_SLASHES);
    }

    /**
     * Web fallback: bridge page that opens the app via universal link.
     */
    public function enrollmentSuccess(Request $request): View
    {
        return view('universal-link.enrollment-success', [
            'courseId' => $request->query('course_id', ''),
            'orderId' => $request->query('order_id', ''),
            'appStoreAppleId' => env('APP_STORE_APPLE_ID', ''),
        ]);
    }
}
