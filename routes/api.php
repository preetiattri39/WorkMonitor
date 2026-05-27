<?php

use App\Http\Controllers\Api\DashboardApiController;
use App\Http\Controllers\Api\MonitoringController;
use App\Http\Controllers\Api\TaskApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function (): void {
    Route::get('/me', fn (Request $request) => $request->user());
    Route::get('/dashboard/overview', [DashboardApiController::class, 'overview']);
    Route::get('/dashboard/summary', [DashboardApiController::class, 'summary']);
    Route::get('/dashboard/charts', [DashboardApiController::class, 'charts']);
    Route::post('/monitoring/activity', [MonitoringController::class, 'storeActivity']);
    Route::post('/monitoring/screenshot', [MonitoringController::class, 'storeScreenshot']);
    Route::apiResource('tasks', TaskApiController::class)
        ->only(['index', 'store', 'update', 'destroy'])
        ->names('api.tasks');
});
