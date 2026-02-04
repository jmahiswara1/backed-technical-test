<?php

namespace App\Services;

use App\Models\LeaveQuota;
use App\Repositories\Contracts\LeaveQuotaRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class LeaveQuotaService
{
    public function __construct(private LeaveQuotaRepositoryInterface $leaveQuotas)
    {
    }

    public function ensureQuota(string $userId, string $startDate, int $totalDays): LeaveQuota
    {
        $year = Carbon::parse($startDate)->year;

        $quota = $this->leaveQuotas->findByUserYear($userId, $year);

        if (!$quota) {
            $quota = $this->leaveQuotas->create([
                'user_id' => $userId,
                'year' => $year,
                'total_quota' => 12,
                'used_quota' => 0,
                'remaining_quota' => 12,
            ]);
        }

        if ($quota->remaining_quota < $totalDays) {
            throw ValidationException::withMessages([
                'quota' => ['Sisa kuota cuti tidak mencukupi.'],
            ]);
        }

        return $quota;
    }

    public function adjustQuota(string $userId, string $startDate, int $totalDays): LeaveQuota
    {
        $year = Carbon::parse($startDate)->year;
        $quota = $this->leaveQuotas->findByUserYear($userId, $year);

        if (!$quota) {
            $quota = $this->leaveQuotas->create([
                'user_id' => $userId,
                'year' => $year,
                'total_quota' => 12,
                'used_quota' => 0,
                'remaining_quota' => 12,
            ]);
        }

        return $this->leaveQuotas->update($quota, [
            'used_quota' => $quota->used_quota + $totalDays,
            'remaining_quota' => $quota->remaining_quota - $totalDays,
        ]);
    }
}
