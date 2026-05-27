<?php

namespace Database\Factories;

use App\Enums\RoleEnum;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected static ?string $password = null;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => RoleEnum::EMPLOYEE,
            'job_title' => fake()->jobTitle(),
            'department' => fake()->randomElement(['Engineering', 'Operations', 'HR', 'Support']),
            'timezone' => 'UTC',
            'is_active' => true,
        ];
    }
}
