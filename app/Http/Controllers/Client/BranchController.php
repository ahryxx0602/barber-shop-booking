<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\View\View;

class BranchController extends Controller
{
    public function index(): View
    {
        $branches = Branch::where('is_active', true)
            ->withCount('barbers')
            ->orderBy('name')
            ->get();

        return view('client.branches.index', compact('branches'));
    }
}
