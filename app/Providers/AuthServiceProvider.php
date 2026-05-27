<?php

namespace App\Providers;

use App\Enums\RoleEnum;
use App\Models\Attendance;
use App\Policies\AttendancePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Attendance::class => AttendancePolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        Gate::before(function ($user) {
            return $user->role === RoleEnum::ADMIN ? true : null;
        });
    }
}
