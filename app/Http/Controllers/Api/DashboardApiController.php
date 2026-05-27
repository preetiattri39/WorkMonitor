<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DashboardAnalyticsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardApiController extends Controller
{
    public function __construct(private readonly DashboardAnalyticsService $analytics)
    {
    }

    public function summary(Request $request): JsonResponse
    {
        return response()->json($this->analytics->summary($request->user()));
    }

    public function overview(Request $request): JsonResponse
    {
        return response()->json($this->analytics->overview($request->user()));
    }

    public function charts(Request $request): JsonResponse
    {
        return response()->json($this->analytics->charts($request->user()));
    }
}
