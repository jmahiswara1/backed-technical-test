<?php

namespace Tests\Feature;

use App\Models\LeaveQuota;
use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LeaveManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_returns_token_and_user(): void
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => 'password',
            'role' => 'employee',
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('user.id', $user->id)
            ->assertJsonPath('user.role', 'employee')
            ->assertJsonStructure(['token', 'user']);
    }

    public function test_employee_can_submit_leave_request(): void
    {
        $employee = User::factory()->create(['role' => 'employee']);
        Sanctum::actingAs($employee);

        $response = $this->postJson('/api/leave-requests', [
            'start_date' => '2026-02-10',
            'end_date' => '2026-02-12',
            'reason' => 'Family event',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('leave_requests', [
            'user_id' => $employee->id,
            'status' => 'pending',
            'total_days' => 3,
        ]);
    }

    public function test_admin_can_approve_leave_request_and_decrement_quota(): void
    {
        $employee = User::factory()->create(['role' => 'employee']);
        $admin = User::factory()->create(['role' => 'admin']);

        $leaveRequest = LeaveRequest::query()->create([
            'user_id' => $employee->id,
            'start_date' => '2026-02-10',
            'end_date' => '2026-02-10',
            'reason' => 'Personal',
            'status' => 'pending',
            'total_days' => 1,
        ]);

        Sanctum::actingAs($admin);

        $response = $this->postJson('/api/admin/leave-requests/'.$leaveRequest->id.'/approve');

        $response->assertOk();
        $this->assertDatabaseHas('leave_requests', [
            'id' => $leaveRequest->id,
            'status' => 'approved',
            'approved_by' => $admin->id,
        ]);

        $quota = LeaveQuota::query()->where('user_id', $employee->id)->where('year', 2026)->first();
        $this->assertNotNull($quota);
        $this->assertSame(1, $quota->used_quota);
        $this->assertSame(11, $quota->remaining_quota);
    }
}
