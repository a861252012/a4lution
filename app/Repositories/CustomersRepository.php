<?php

namespace App\Repositories;

use App\Models\Customers;
use Illuminate\Support\Facades\DB;

class CustomersRepository
{
    public function __construct()
    {
    }

    public function getAllClientCode()
    {
        return Customers::select('client_code')
            ->where('active', 1)
            ->pluck('client_code');
    }
}
