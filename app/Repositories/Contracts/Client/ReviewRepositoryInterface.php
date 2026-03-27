<?php

namespace App\Repositories\Contracts\Client;

use App\Repositories\Contracts\BaseRepositoryInterface;

interface ReviewRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Tính rating trung bình của barber từ tất cả reviews.
     */
    public function getAverageRatingForBarber(int $barberId): ?float;
}
