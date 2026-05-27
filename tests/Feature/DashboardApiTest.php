<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DashboardApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_fetch_dashboard_summary(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/dashboard/summary');

        $response->assertOk()
            ->assertJsonStructure([
                'employees',
                'active_projects',
                'open_tasks',
                'completed_tasks_today',
                'hours_logged_today',
                'avg_productivity_score',
                'attendance_rate',
                'pending_leave_requests',
            ]);
    }
}
