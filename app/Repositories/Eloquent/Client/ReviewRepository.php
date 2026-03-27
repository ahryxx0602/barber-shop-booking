<?php

namespace App\Repositories\Eloquent\Client;

use App\Models\Review;
use App\Repositories\Contracts\Client\ReviewRepositoryInterface;

class ReviewRepository extends BaseRepository implements ReviewRepositoryInterface
{
    public function __construct(Review $model)
    {
        parent::__construct($model);
    }

    public function getAverageRatingForBarber(int $barberId): ?float
    {
        return $this->model->where('barber_id', $barberId)->avg('rating');
    }
}
