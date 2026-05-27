<?php

namespace App\Http\Controllers;

use App\Services\DashboardAnalyticsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function __construct(private readonly DashboardAnalyticsService $analytics)
    {
    }

    public function __invoke(Request $request): View
    {
        return view('app', [
            'overview' => $this->analytics->overview($request->user()),
        ]);
    }

    public function overview(Request $request): JsonResponse
    {
        return response()->json($this->analytics->overview($request->user()));
    }
}
