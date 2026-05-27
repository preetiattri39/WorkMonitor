<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScreenshotCapture extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'captured_at',
        'image_path',
        'blur_sensitive',
        'productivity_label',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'captured_at' => 'datetime',
            'blur_sensitive' => 'boolean',
            'metadata' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
