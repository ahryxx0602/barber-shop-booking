<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\Admin\CreateCouponData;
use App\DTOs\Admin\UpdateCouponData;
use App\Enums\CouponAppliesTo;
use App\Enums\CouponType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCouponRequest;
use App\Http\Requests\Admin\UpdateCouponRequest;
use App\Models\Coupon;
use App\Services\CouponService;

class CouponController extends Controller
{
    public function __construct(
        protected CouponService $couponService,
    ) {}

    public function index()
    {
        $coupons = Coupon::orderByDesc('created_at')->paginate(20);
        return view('admin.coupons.index', compact('coupons'));
    }

    public function create()
    {
        $types = CouponType::cases();
        $appliesToOptions = CouponAppliesTo::cases();
        return view('admin.coupons.create', compact('types', 'appliesToOptions'));
    }

    public function store(StoreCouponRequest $request)
    {
        $this->couponService->createCoupon(
            CreateCouponData::fromRequest($request)
        );

        return redirect()->route('admin.coupons.index')->with('success', 'Tạo mã giảm giá thành công!');
    }

    public function edit(Coupon $coupon)
    {
        $types = CouponType::cases();
        $appliesToOptions = CouponAppliesTo::cases();
        return view('admin.coupons.edit', compact('coupon', 'types', 'appliesToOptions'));
    }

    public function update(UpdateCouponRequest $request, Coupon $coupon)
    {
        $this->couponService->updateCoupon(
            $coupon,
            UpdateCouponData::fromRequest($request)
        );

        return redirect()->route('admin.coupons.index')->with('success', 'Cập nhật mã giảm giá thành công!');
    }

    public function destroy(Coupon $coupon)
    {
        $this->couponService->deleteCoupon($coupon);
        return redirect()->route('admin.coupons.index')->with('success', 'Xóa mã giảm giá thành công!');
    }
}
