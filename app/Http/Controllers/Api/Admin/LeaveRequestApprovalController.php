<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\LeaveRequestResource;
use App\Repositories\Contracts\LeaveRequestRepositoryInterface;
use App\Services\LeaveRequestService;
use Illuminate\Http\JsonResponse;

class LeaveRequestApprovalController extends Controller
{
    public function __construct(
        private LeaveRequestService $leaveRequestService,
        private LeaveRequestRepositoryInterface $leaveRequests
    ) {
    }

    public function pending(): JsonResponse
    {
        $items = $this->leaveRequests->findPending();

        return response()->json([
            'data' => LeaveRequestResource::collection($items),
            'meta' => [
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total(),
            ],
        ]);
    }

    public function approve(string $id): JsonResponse
    {
        $leaveRequest = $this->leaveRequestService->approveLeave($id, request()->user());

        return response()->json([
            'data' => LeaveRequestResource::make($leaveRequest),
        ]);
    }

    public function reject(string $id): JsonResponse
    {
        $leaveRequest = $this->leaveRequestService->rejectLeave($id, request()->user());

        return response()->json([
            'data' => LeaveRequestResource::make($leaveRequest),
        ]);
    }
}
