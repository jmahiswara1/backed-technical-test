<?php

namespace App\Services;

use App\Models\LeaveRequest;
use App\Models\User;
use App\Repositories\Contracts\LeaveRequestRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LeaveRequestService
{
    public function __construct(
        private LeaveRequestRepositoryInterface $leaveRequests,
        private LeaveQuotaService $leaveQuotaService
    ) {
    }

    public function submitLeave(array $data, User $user): LeaveRequest
    {
        $this->validateDateRange($data['start_date'], $data['end_date']);
        $totalDays = $this->calculateTotalDays($data['start_date'], $data['end_date']);
        $this->leaveQuotaService->ensureQuota($user->id, $data['start_date'], $totalDays);

        return $this->leaveRequests->create([
            'id' => (string) Str::uuid(),
            'user_id' => $user->id,
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'reason' => $data['reason'],
            'attachment_path' => $data['attachment_path'] ?? null,
            'status' => 'pending',
            'total_days' => $totalDays,
        ]);
    }

    public function approveLeave(string $requestId, User $admin): LeaveRequest
    {
        return DB::transaction(function () use ($requestId, $admin) {
            $request = $this->leaveRequests->findById($requestId);

            if (!$request) {
                throw ValidationException::withMessages([
                    'request' => ['Permintaan cuti tidak ditemukan.'],
                ]);
            }

            if ($request->status !== 'pending') {
                throw ValidationException::withMessages([
                    'status' => ['Permintaan cuti sudah diproses.'],
                ]);
            }

            $this->leaveQuotaService->adjustQuota($request->user_id, $request->start_date, $request->total_days);

            return $this->leaveRequests->updateStatus($requestId, [
                'status' => 'approved',
                'approved_by' => $admin->id,
            ]);
        });
    }

    public function rejectLeave(string $requestId, User $admin): LeaveRequest
    {
        $request = $this->leaveRequests->findById($requestId);

        if (!$request) {
            throw ValidationException::withMessages([
                'request' => ['Permintaan cuti tidak ditemukan.'],
            ]);
        }

        if ($request->status !== 'pending') {
            throw ValidationException::withMessages([
                'status' => ['Permintaan cuti sudah diproses.'],
            ]);
        }

        return $this->leaveRequests->updateStatus($requestId, [
            'status' => 'rejected',
            'approved_by' => $admin->id,
        ]);
    }

    public function calculateTotalDays(string $startDate, string $endDate): int
    {
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->startOfDay();

        return $start->diffInDays($end) + 1;
    }

    private function validateDateRange(string $startDate, string $endDate): void
    {
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->startOfDay();

        if ($start->greaterThan($end)) {
            throw ValidationException::withMessages([
                'date' => ['Tanggal mulai harus sebelum tanggal selesai.'],
            ]);
        }
    }
}
