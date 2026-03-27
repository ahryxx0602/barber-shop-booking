<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barber;
use App\Services\Admin\CommissionService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CommissionController extends Controller
{
    public function __construct(
        private CommissionService $commissionService,
    ) {}

    /**
     * Trang tổng quan hoa hồng + danh sách barber với tỷ lệ.
     */
    public function index(Request $request): View
    {
        // Lọc theo thời gian
        $startDate = $request->input('start_date')
            ? Carbon::parse($request->input('start_date'))->startOfDay()
            : now()->startOfMonth();
        $endDate = $request->input('end_date')
            ? Carbon::parse($request->input('end_date'))->endOfDay()
            : now()->endOfMonth();

        $overview  = $this->commissionService->getMonthlyOverview();
        $barbers   = $this->commissionService->getSummaryByBarber($startDate, $endDate);
        $history   = $this->commissionService->getHistory(
            barberId: $request->input('barber_id') ? (int) $request->input('barber_id') : null,
            startDate: $startDate,
            endDate: $endDate,
        );

        $allBarbers = Barber::with('user:id,name')->where('is_active', true)->get();

        return view('admin.commissions.index', compact(
            'overview', 'barbers', 'history', 'allBarbers', 'startDate', 'endDate'
        ));
    }

    /**
     * Cập nhật tỷ lệ hoa hồng cho 1 barber (AJAX).
     */
    public function updateRate(Request $request, Barber $barber): JsonResponse
    {
        $request->validate([
            'commission_rate' => 'required|numeric|min:0|max:100',
        ], [
            'commission_rate.required' => 'Vui lòng nhập tỷ lệ hoa hồng.',
            'commission_rate.numeric'  => 'Tỷ lệ hoa hồng phải là số.',
            'commission_rate.min'      => 'Tỷ lệ hoa hồng không được âm.',
            'commission_rate.max'      => 'Tỷ lệ hoa hồng tối đa 100%.',
        ]);

        $this->commissionService->updateRate($barber, (float) $request->input('commission_rate'));

        return response()->json([
            'success' => true,
            'message' => 'Đã cập nhật tỷ lệ hoa hồng.',
            'commission_rate' => $barber->fresh()->commission_rate,
        ]);
    }

    /**
     * Cập nhật tỷ lệ hoa hồng hàng loạt.
     */
    public function bulkUpdateRate(Request $request): JsonResponse
    {
        $request->validate([
            'commission_rate' => 'required|numeric|min:0|max:100',
            'barber_ids'      => 'nullable|array',
            'barber_ids.*'    => 'exists:barbers,id',
        ], [
            'commission_rate.required' => 'Vui lòng nhập tỷ lệ hoa hồng.',
        ]);

        $count = $this->commissionService->bulkUpdateRate(
            (float) $request->input('commission_rate'),
            $request->input('barber_ids'),
        );

        return response()->json([
            'success' => true,
            'message' => "Đã cập nhật tỷ lệ hoa hồng cho {$count} thợ cắt.",
            'count'   => $count,
        ]);
    }
}
