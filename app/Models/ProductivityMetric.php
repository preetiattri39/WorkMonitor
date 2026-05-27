<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductivityMetric extends Model
{
    protected $fillable = [
        'user_id',
        'metric_date',
        'focused_minutes',
        'productive_minutes',
        'idle_minutes',
        'attendance_minutes',
        'tasks_completed',
        'score',
    ];

    protected function casts(): array
    {
        return [
            'metric_date' => 'date',
            'score' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
