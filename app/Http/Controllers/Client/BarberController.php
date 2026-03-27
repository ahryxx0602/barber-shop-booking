<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Barber;
use App\Services\BarberService;
use App\Services\BranchService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BarberController extends Controller
{
    public function __construct(
        protected BarberService $barberService,
        protected BranchService $brancheService
    ) {
    }

    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'branch_id']);

        $barbers = $this->barberService->getActiveBarbersWithFilters($filters);
        $branches = $this->brancheService->getActiveBranches();

        return view('client.barbers.index', compact('barbers', 'branches'));
    }

    public function show(Barber $barber): View
    {
        $this->barberService->loadBarberDetails($barber);

        return view('client.barbers.show', compact('barber'));
    }
}
