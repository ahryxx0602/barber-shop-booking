<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\StoreWaitlistRequest;
use App\Services\WaitlistService;
use Illuminate\Http\Request;

class WaitlistController extends Controller
{
    public function store(StoreWaitlistRequest $request, WaitlistService $waitlistService)
    {
        $validated = $request->validated();

        $waitlistService->register(
            $request->user(),
            $validated['barber_id'],
            $validated['desired_date'],
            $validated['desired_time'] ?? null,
        );

        return back()->with('success', 'Đăng ký chờ thành công! Bạn sẽ được thông báo khi có slot trống.');
    }
}
