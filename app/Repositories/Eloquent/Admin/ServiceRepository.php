<?php

namespace App\Repositories\Eloquent\Admin;

use App\Repositories\Eloquent\BaseRepository;
use App\Models\Service;
use App\Repositories\Contracts\Admin\ServiceRepositoryInterface;

class ServiceRepository extends BaseRepository implements ServiceRepositoryInterface
{
    public function __construct(Service $model)
    {
        parent::__construct($model);
    }
}
