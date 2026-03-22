<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\StoreReviewRequest;
use App\Services\ReviewService;
use Illuminate\Http\RedirectResponse;

class ReviewController extends Controller
{
    public function __construct(
        protected ReviewService $reviewService,
    ) {}

    public function store(StoreReviewRequest $request): RedirectResponse
    {
        $this->reviewService->store($request->validated(), $request->user());

        return back()->with('success', 'Cam on ban da danh gia!');
    }
}
