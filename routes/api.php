<?php

use App\Http\Controllers\Api\Admin\LeaveRequestApprovalController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LeaveRequestController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::get('google/redirect', [AuthController::class, 'googleRedirect']);
    Route::get('google/callback', [AuthController::class, 'googleCallback']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('me', [AuthController::class, 'me']);

    Route::middleware('role:employee')->group(function () {
        Route::get('leave-requests', [LeaveRequestController::class, 'index']);
        Route::post('leave-requests', [LeaveRequestController::class, 'store']);
    });

    Route::prefix('admin')->middleware('role:admin')->group(function () {
        Route::get('leave-requests/pending', [LeaveRequestApprovalController::class, 'pending']);
        Route::post('leave-requests/{id}/approve', [LeaveRequestApprovalController::class, 'approve']);
        Route::post('leave-requests/{id}/reject', [LeaveRequestApprovalController::class, 'reject']);
    });
});
