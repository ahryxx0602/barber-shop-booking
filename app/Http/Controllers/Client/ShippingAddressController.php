<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAddressRequest;
use App\DTOs\ShippingAddressData;
use App\Models\ShippingAddress;
use Illuminate\Http\JsonResponse;

class ShippingAddressController extends Controller
{
    /**
     * Thêm địa chỉ giao hàng mới (AJAX).
     */
    public function store(StoreAddressRequest $request): JsonResponse
    {
        $data = ShippingAddressData::fromRequest($request);

        // Nếu đặt làm mặc định → bỏ mặc định các địa chỉ cũ
        if ($data->is_default) {
            $request->user()->shippingAddresses()->update(['is_default' => false]);
        }

        // Nếu là địa chỉ đầu tiên → tự động đặt mặc định
        $isFirst = $request->user()->shippingAddresses()->count() === 0;

        $address = $request->user()->shippingAddresses()->create([
            'recipient_name' => $data->recipient_name,
            'phone'          => $data->phone,
            'address'        => $data->address,
            'ward'           => $data->ward,
            'district'       => $data->district,
            'city'           => $data->city,
            'latitude'       => $data->latitude,
            'longitude'      => $data->longitude,
            'is_default'     => $isFirst || $data->is_default,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã thêm địa chỉ thành công.',
            'address' => $address,
        ]);
    }

    /**
     * Đặt địa chỉ làm mặc định (AJAX).
     */
    public function setDefault(ShippingAddress $address): JsonResponse
    {
        // Chỉ cho phép user sở hữu địa chỉ
        if ($address->user_id !== auth()->id()) {
            abort(403);
        }

        // Bỏ mặc định tất cả
        auth()->user()->shippingAddresses()->update(['is_default' => false]);

        // Đặt mặc định
        $address->update(['is_default' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Đã đặt làm địa chỉ mặc định.',
        ]);
    }

    /**
     * Xóa địa chỉ (AJAX). Không xóa nếu đã có đơn hàng.
     */
    public function destroy(ShippingAddress $address): JsonResponse
    {
        if ($address->user_id !== auth()->id()) {
            abort(403);
        }

        // Kiểm tra xem địa chỉ đã được dùng trong đơn hàng chưa
        if ($address->orders()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể xóa địa chỉ đã dùng cho đơn hàng.',
            ], 422);
        }

        $address->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa địa chỉ.',
        ]);
    }
}
