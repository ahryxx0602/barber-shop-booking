<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __construct(private ReportService $reportService) {}

    public function index(): View
    {
        $overview = $this->reportService->getMonthlyOverview();
        $dailyRevenue = $this->reportService->getDailyRevenue();
        $availableYears = $this->reportService->getAvailableYears();
        $topBarbers = $this->reportService->getTopBarbers();
        $topServices = $this->reportService->getTopServices();

        return view('admin.reports.index', compact(
            'overview', 'dailyRevenue', 'availableYears', 'topBarbers', 'topServices'
        ));
    }

    /**
     * API trả dữ liệu chart theo mode.
     */
    public function chartData(Request $request): JsonResponse
    {
        $mode = $request->input('mode', 'recent');

        $result = match ($mode) {
            'monthly' => $this->reportService->getDailyRevenue(
                (int) $request->input('month', now()->month),
                (int) $request->input('year', now()->year),
            ),
            'yearly' => $this->reportService->getMonthlyRevenue(
                (int) $request->input('year', now()->year),
            ),
            default => $this->reportService->getDailyRevenue(),
        };

        return response()->json($result);
    }
}
