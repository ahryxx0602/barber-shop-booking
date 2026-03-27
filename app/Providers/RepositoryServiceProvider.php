<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Danh sách bind Interface => Implementation.
     * Sẽ bổ sung thêm khi triển khai từng Phase.
     */
    protected array $repositories = [
        // Phase 1: Admin
        \App\Repositories\Contracts\Admin\BarberRepositoryInterface::class => \App\Repositories\Eloquent\Admin\BarberRepository::class,
        \App\Repositories\Contracts\Admin\UserRepositoryInterface::class => \App\Repositories\Eloquent\Admin\UserRepository::class,
        \App\Repositories\Contracts\Admin\BranchRepositoryInterface::class => \App\Repositories\Eloquent\Admin\BranchRepository::class,
        \App\Repositories\Contracts\Admin\ServiceRepositoryInterface::class => \App\Repositories\Eloquent\Admin\ServiceRepository::class,
        \App\Repositories\Contracts\Admin\ProductRepositoryInterface::class => \App\Repositories\Eloquent\Admin\ProductRepository::class,
        \App\Repositories\Contracts\Admin\CouponRepositoryInterface::class => \App\Repositories\Eloquent\Admin\CouponRepository::class,

        // Phase 2: Barber
        \App\Repositories\Contracts\Barber\BookingRepositoryInterface::class => \App\Repositories\Eloquent\Barber\BookingRepository::class,
        \App\Repositories\Contracts\Barber\ScheduleRepositoryInterface::class => \App\Repositories\Eloquent\Barber\ScheduleRepository::class,
        \App\Repositories\Contracts\Barber\BarberLeaveRepositoryInterface::class => \App\Repositories\Eloquent\Barber\BarberLeaveRepository::class,

        // Phase 3: Client
        \App\Repositories\Contracts\Client\OrderRepositoryInterface::class => \App\Repositories\Eloquent\Client\OrderRepository::class,
        \App\Repositories\Contracts\Client\ReviewRepositoryInterface::class => \App\Repositories\Eloquent\Client\ReviewRepository::class,
    ];

    public function register(): void
    {
        foreach ($this->repositories as $interface => $implementation) {
            $this->app->bind($interface, $implementation);
        }
    }

    public function boot(): void
    {
        //
    }
}
