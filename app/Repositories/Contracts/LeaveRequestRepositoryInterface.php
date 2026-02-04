<?php

namespace App\Repositories\Contracts;

use App\Models\LeaveRequest;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface LeaveRequestRepositoryInterface
{
    public function create(array $data): LeaveRequest;

    public function findById(string $id): ?LeaveRequest;

    public function findPending(int $perPage = 15): LengthAwarePaginator;

    public function findByUser(string $userId, int $perPage = 15): LengthAwarePaginator;

    public function updateStatus(string $id, array $data): LeaveRequest;
}
