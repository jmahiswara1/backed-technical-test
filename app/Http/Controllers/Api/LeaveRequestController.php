<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubmitLeaveRequest;
use App\Http\Resources\LeaveRequestResource;
use App\Repositories\Contracts\LeaveRequestRepositoryInterface;
use App\Services\LeaveRequestService;
use Illuminate\Http\JsonResponse;

class LeaveRequestController extends Controller
{
    public function __construct(
        private LeaveRequestService $leaveRequestService,
        private LeaveRequestRepositoryInterface $leaveRequests
    ) {
    }

    public function index(): JsonResponse
    {
        $user = request()->user();

        $items = $this->leaveRequests->findByUser($user->id);

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

    public function store(SubmitLeaveRequest $request): JsonResponse
    {
        $payload = $request->validated();

        if ($request->hasFile('attachment')) {
            $payload['attachment_path'] = $request->file('attachment')->store('leave-attachments', 'public');
        }

        $leaveRequest = $this->leaveRequestService->submitLeave($payload, $request->user());

        return response()->json([
            'data' => LeaveRequestResource::make($leaveRequest),
        ], 201);
    }
}
