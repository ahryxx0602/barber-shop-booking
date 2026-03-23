<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Barber;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BarberController extends Controller
{
    public function index(Request $request): View
    {
        // Tối ưu N+1 Query (Issue #6): Thêm withCount('reviews') để tránh lazy load khi hiển thị số lượng đánh giá
        $barbers = Barber::with('user')
            ->withCount('reviews')
            ->where('is_active', true)
            ->when($request->search, function ($query, $search) {
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            })
            ->get();

        return view('client.barbers.index', compact('barbers'));
    }

    public function show(Barber $barber): View
    {
        $barber->load(['user', 'reviews.customer', 'workingSchedules']);

        return view('client.barbers.show', compact('barber'));
    }
}
