<?php

namespace App\Policies;

use App\Enums\RoleEnum;
use App\Models\Attendance;
use App\Models\User;

class AttendancePolicy
{
    public function update(User $user, Attendance $attendance): bool
    {
        return $user->role !== RoleEnum::EMPLOYEE || $attendance->user_id === $user->id;
    }
}
