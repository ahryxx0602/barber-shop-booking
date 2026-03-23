<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Barber;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FavoriteBarberController extends Controller
{
    /**
     * Toggle favorite barber (thêm/xóa khỏi danh sách yêu thích).
     */
    public function toggle(Request $request, Barber $barber): JsonResponse
    {
        $user = $request->user();

        if ($user->favoriteBarbers()->where('barber_id', $barber->id)->exists()) {
            $user->favoriteBarbers()->detach($barber->id);
            return response()->json(['favorited' => false, 'message' => 'Đã bỏ yêu thích.']);
        }

        $user->favoriteBarbers()->attach($barber->id);
        return response()->json(['favorited' => true, 'message' => 'Đã thêm vào yêu thích!']);
    }
}
