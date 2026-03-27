<?php

namespace App\Http\Controllers\Client;

use App\DTOs\Client\StoreReviewData;
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
        $this->reviewService->store(StoreReviewData::fromRequest($request), $request->user());

        return back()->with('success', 'Cảm ơn bạn đã đánh giá!');
    }
}
