<?php

use App\Http\Controllers\Admin\BarberController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\CommissionController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\LeaveController as AdminLeaveController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\SystemLogController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::resource('services', ServiceController::class)->except(['show']);
        Route::resource('barbers', BarberController::class)->except(['show']);
        Route::resource('branches', BranchController::class)->except(['show']);

        // Sản phẩm (E-commerce)
        Route::resource('products', ProductController::class)->except(['show']);
        Route::patch('products/{product}/toggle', [ProductController::class, 'toggleActive'])->name('products.toggle');

        // Lịch làm việc
        Route::get('schedules', [ScheduleController::class, 'index'])->name('schedules.index');
        Route::get('schedules/{barber}/edit', [ScheduleController::class, 'edit'])->name('schedules.edit');
        Route::put('schedules/{barber}', [ScheduleController::class, 'update'])->name('schedules.update');

        // Booking
        Route::get('bookings', [AdminBookingController::class, 'index'])->name('bookings.index');

        // Người dùng
        Route::resource('users', UserController::class)->only(['index', 'show', 'edit', 'update']);
        Route::patch('users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggleActive');

        // Quản lý ngày nghỉ
        Route::get('leaves', [AdminLeaveController::class, 'index'])->name('leaves.index');
        Route::patch('leaves/{leave}/approve', [AdminLeaveController::class, 'approve'])->name('leaves.approve');
        Route::patch('leaves/{leave}/reject', [AdminLeaveController::class, 'reject'])->name('leaves.reject');

        // Mã giảm giá
        Route::resource('coupons', CouponController::class)->except(['show']);

        // Hoa hồng (Commission)
        Route::get('commissions', [CommissionController::class, 'index'])->name('commissions.index');
        Route::patch('commissions/{barber}/rate', [CommissionController::class, 'updateRate'])->name('commissions.updateRate');
        Route::patch('commissions/bulk-rate', [CommissionController::class, 'bulkUpdateRate'])->name('commissions.bulkUpdateRate');

        // Báo cáo
        Route::get('reports/chart-data', [ReportController::class, 'chartData'])->name('reports.chartData');
        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');

        // Hệ thống
        Route::get('system/logs', [SystemLogController::class, 'index'])->name('system.logs');
    });
