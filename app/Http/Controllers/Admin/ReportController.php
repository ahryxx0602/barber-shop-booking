<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Services\ReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __construct(private ReportService $reportService) {}

    public function index(Request $request): View
    {
        $branchId = $request->input('branch_id') ? (int) $request->input('branch_id') : null;

        $overview = $this->reportService->getMonthlyOverview(branchId: $branchId);
        $dailyRevenue = $this->reportService->getDailyRevenue(branchId: $branchId);
        $availableYears = $this->reportService->getAvailableYears();
        $topBarbers = $this->reportService->getTopBarbers(branchId: $branchId);
        $topServices = $this->reportService->getTopServices(branchId: $branchId);
        $branches = Branch::where('is_active', true)->orderBy('name')->get();

        // Product stats
        $productOverview = $this->reportService->getProductMonthlyOverview();
        $productDailyRevenue = $this->reportService->getProductDailyRevenue();
        $topSellingProducts = $this->reportService->getTopSellingProducts(5);

        return view('admin.reports.index', compact(
            'overview', 'dailyRevenue', 'availableYears', 'topBarbers', 'topServices', 'branches', 'branchId',
            'productOverview', 'productDailyRevenue', 'topSellingProducts'
        ));
    }

    /**
     * API trả dữ liệu chart theo mode và type.
     */
    public function chartData(Request $request): JsonResponse
    {
        $mode = $request->input('mode', 'recent');
        $type = $request->input('type', 'service');
        $branchId = $request->input('branch_id') ? (int) $request->input('branch_id') : null;

        if ($type === 'product') {
            $result = match ($mode) {
                'monthly' => $this->reportService->getProductDailyRevenue(
                    (int) $request->input('month', now()->month),
                    (int) $request->input('year', now()->year),
                ),
                'yearly' => $this->reportService->getProductMonthlyRevenue(
                    (int) $request->input('year', now()->year),
                ),
                default => $this->reportService->getProductDailyRevenue(),
            };
        } else {
            $result = match ($mode) {
                'monthly' => $this->reportService->getDailyRevenue(
                    (int) $request->input('month', now()->month),
                    (int) $request->input('year', now()->year),
                    $branchId,
                ),
                'yearly' => $this->reportService->getMonthlyRevenue(
                    (int) $request->input('year', now()->year),
                    $branchId,
                ),
                default => $this->reportService->getDailyRevenue(branchId: $branchId),
            };
        }

        return response()->json($result);
    }
}
