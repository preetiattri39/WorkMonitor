<?php

return [
    'productivity' => [
        'focused_weight' => 0.45,
        'attendance_weight' => 0.2,
        'task_weight' => 0.2,
        'activity_weight' => 0.15,
        'low_score_threshold' => 45,
        'excellent_score_threshold' => 85,
    ],
    'screenshots' => [
        'disk' => env('FILESYSTEM_DISK', 'public'),
        'max_size_kb' => 4096,
        'capture_interval_minutes' => 10,
    ],
];
