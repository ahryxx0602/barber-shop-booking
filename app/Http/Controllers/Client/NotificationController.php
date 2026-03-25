<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Đánh dấu tất cả notification là đã đọc.
     */
    public function readAll(Request $request): RedirectResponse
    {
        $request->user()->notifications()->where('is_read', false)->update(['is_read' => true]);

        return back();
    }

    /**
     * Poll notification mới (AJAX).
     */
    public function poll(Request $request): JsonResponse
    {
        $user = $request->user();

        $unreadNotifications = $user->notifications()
            ->where('is_read', false)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $unreadCount = $user->notifications()->where('is_read', false)->count();

        return response()->json([
            'count' => $unreadCount,
            'html' => view('partials.notification-items', compact('unreadNotifications'))->render(),
        ]);
    }
}
