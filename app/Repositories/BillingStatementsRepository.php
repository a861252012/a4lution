<?php

namespace App\Repositories;

use App\Models\BillingStatements;

class BillingStatementsRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(new BillingStatements);
    }

}
