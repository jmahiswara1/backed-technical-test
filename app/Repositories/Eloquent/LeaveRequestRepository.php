<?php

namespace App\Repositories\Eloquent;

use App\Models\LeaveRequest;
use App\Repositories\Contracts\LeaveRequestRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class LeaveRequestRepository implements LeaveRequestRepositoryInterface
{
    public function create(array $data): LeaveRequest
    {
        return LeaveRequest::query()->create($data);
    }

    public function findById(string $id): ?LeaveRequest
    {
        return LeaveRequest::query()->find($id);
    }

    public function findPending(int $perPage = 15): LengthAwarePaginator
    {
        return LeaveRequest::query()
            ->where('status', 'pending')
            ->latest()
            ->paginate($perPage);
    }

    public function findByUser(string $userId, int $perPage = 15): LengthAwarePaginator
    {
        return LeaveRequest::query()
            ->where('user_id', $userId)
            ->latest()
            ->paginate($perPage);
    }

    public function updateStatus(string $id, array $data): LeaveRequest
    {
        $leaveRequest = LeaveRequest::query()->findOrFail($id);
        $leaveRequest->fill($data);
        $leaveRequest->save();

        return $leaveRequest;
    }
}
