<?php

namespace App\Http\Controllers\Api;

use App\Events\ProductivitySnapshotCreated;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\ScreenshotCapture;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MonitoringController extends Controller
{
    public function storeActivity(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'logged_at' => ['required', 'date'],
            'keystrokes' => ['required', 'integer', 'min:0'],
            'mouse_clicks' => ['required', 'integer', 'min:0'],
            'active_window_title' => ['nullable', 'string', 'max:255'],
            'active_window_url' => ['nullable', 'string', 'max:2048'],
            'idle_seconds' => ['required', 'integer', 'min:0'],
        ]);

        $payload['activity_score'] = max(0, min(100, (int) (100 - ($payload['idle_seconds'] / 6))));

        $activity = ActivityLog::create($payload + ['user_id' => $request->user()->id]);
        broadcast(new ProductivitySnapshotCreated($request->user(), $activity))->toOthers();

        return response()->json(['message' => 'Activity captured.'], 201);
    }

    public function storeScreenshot(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'captured_at' => ['required', 'date'],
            'screenshot' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:'.config('monitoring.screenshots.max_size_kb')],
            'blur_sensitive' => ['sometimes', 'boolean'],
            'productivity_label' => ['nullable', 'string', 'max:100'],
            'metadata' => ['nullable', 'array'],
        ]);

        $path = $request->file('screenshot')->store('screenshots/'.$request->user()->id, config('monitoring.screenshots.disk'));

        $screenshot = ScreenshotCapture::create([
            'user_id' => $request->user()->id,
            'captured_at' => $payload['captured_at'],
            'image_path' => $path,
            'blur_sensitive' => $payload['blur_sensitive'] ?? false,
            'productivity_label' => $payload['productivity_label'] ?? 'unclassified',
            'metadata' => $payload['metadata'] ?? [],
        ]);

        return response()->json([
            'message' => 'Screenshot uploaded.',
            'path' => Storage::disk(config('monitoring.screenshots.disk'))->url($screenshot->image_path),
        ], 201);
    }
}
