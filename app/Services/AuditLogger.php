<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class AuditLogger
{
    public function __construct(private readonly ?Request $request = null)
    {
    }

    public static function forRequest(Request $request): self
    {
        return new self($request);
    }

    public function created(Model $model): void
    {
        $this->write('created', $model, null, $model->fresh()?->toArray() ?? $model->toArray());
    }

    public function updated(Model $model, array $oldValues): void
    {
        $this->write('updated', $model, $oldValues, $model->fresh()?->toArray() ?? $model->toArray());
    }

    public function deleted(Model $model, array $oldValues): void
    {
        $this->write('deleted', $model, $oldValues, null);
    }

    private function write(string $action, Model $model, ?array $oldValues, ?array $newValues): void
    {
        AuditLog::create([
            'actor_id' => $this->request?->user()?->id,
            'auditable_type' => $model::class,
            'auditable_id' => $model->getKey(),
            'action' => $action,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => $this->request?->ip(),
            'user_agent' => $this->request?->userAgent(),
            'created_at' => now(),
        ]);
    }
}
