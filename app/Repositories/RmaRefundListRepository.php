<?php

namespace App\Repositories;

use App\Models\RmaRefundList;
use Illuminate\Support\Facades\DB;

class RmaRefundListRepository
{
    protected $rmaRefundList;

    public function __construct(RmaRefundList $rmaRefundList)
    {
        $this->rmaRefundList = $rmaRefundList;
    }

    public function insertData(array $data)
    {
        return DB::transaction(function () use ($data) {
            return $this->rmaRefundList->insert($data);
        });
    }
}
