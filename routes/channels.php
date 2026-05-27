<?php

use App\Enums\RoleEnum;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('users.{userId}', fn ($user, int $userId) => (int) $user->id === $userId);
Broadcast::channel('managers', fn ($user) => in_array($user->role, [RoleEnum::ADMIN->value, RoleEnum::MANAGER->value], true));
