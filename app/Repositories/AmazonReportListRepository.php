<?php

namespace App\Repositories;

use App\Models\AmazonReportList;

class AmazonReportListRepository extends BaseRepository
{
    protected AmazonReportList $amazonReportList;

    public function __construct()
    {
        parent::__construct(new AmazonReportList);
    }
}
