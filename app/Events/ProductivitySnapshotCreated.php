<?php

namespace App\Events;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductivitySnapshotCreated implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(public User $user, public ActivityLog $activityLog)
    {
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('users.'.$this->user->id),
            new PrivateChannel('managers'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'productivity.snapshot.created';
    }
}
