<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __construct(private ReportService $reportService) {}

    public function index(): View
    {
        $overview = $this->reportService->getMonthlyOverview();

        return view('admin.reports.index', compact('overview'));
    }
}
