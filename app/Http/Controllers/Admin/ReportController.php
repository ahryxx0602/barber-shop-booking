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

        return view('admin.reports.index', compact(
            'overview', 'dailyRevenue', 'availableYears', 'topBarbers', 'topServices', 'branches', 'branchId'
        ));
    }

    /**
     * API trả dữ liệu chart theo mode.
     */
    public function chartData(Request $request): JsonResponse
    {
        $mode = $request->input('mode', 'recent');
        $branchId = $request->input('branch_id') ? (int) $request->input('branch_id') : null;

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

        return response()->json($result);
    }
}
