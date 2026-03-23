<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\WaitlistService;
use Illuminate\Http\Request;

class WaitlistController extends Controller
{
    public function store(Request $request, WaitlistService $waitlistService)
    {
        $validated = $request->validate([
            'barber_id' => 'required|exists:barbers,id',
            'desired_date' => 'required|date|after:today',
            'desired_time' => 'nullable|date_format:H:i',
        ]);

        $waitlistService->register(
            $request->user(),
            $validated['barber_id'],
            $validated['desired_date'],
            $validated['desired_time'] ?? null,
        );

        return back()->with('success', 'Đăng ký chờ thành công! Bạn sẽ được thông báo khi có slot trống.');
    }
}
