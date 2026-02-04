<?php

namespace App\Repositories\Eloquent;

use App\Models\LeaveQuota;
use App\Repositories\Contracts\LeaveQuotaRepositoryInterface;

class LeaveQuotaRepository implements LeaveQuotaRepositoryInterface
{
    public function findByUserYear(string $userId, int $year): ?LeaveQuota
    {
        return LeaveQuota::query()
            ->where('user_id', $userId)
            ->where('year', $year)
            ->first();
    }

    public function create(array $data): LeaveQuota
    {
        return LeaveQuota::query()->create($data);
    }

    public function update(LeaveQuota $leaveQuota, array $data): LeaveQuota
    {
        $leaveQuota->fill($data);
        $leaveQuota->save();

        return $leaveQuota;
    }
}
