<?php

namespace App\Providers;

use App\Repositories\Contracts\LeaveQuotaRepositoryInterface;
use App\Repositories\Contracts\LeaveRequestRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Eloquent\LeaveQuotaRepository;
use App\Repositories\Eloquent\LeaveRequestRepository;
use App\Repositories\Eloquent\UserRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(LeaveQuotaRepositoryInterface::class, LeaveQuotaRepository::class);
        $this->app->bind(LeaveRequestRepositoryInterface::class, LeaveRequestRepository::class);
    }

    public function boot(): void
    {
    }
}
