<?php

namespace App\Repositories\Contracts;

use App\Models\LeaveQuota;

interface LeaveQuotaRepositoryInterface
{
    public function findByUserYear(string $userId, int $year): ?LeaveQuota;

    public function create(array $data): LeaveQuota;

    public function update(LeaveQuota $leaveQuota, array $data): LeaveQuota;
}
