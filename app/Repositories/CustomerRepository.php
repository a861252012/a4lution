<?php

namespace App\Repositories;

use App\Models\Customers;
use App\Support\LaravelLoggerUtil;

class CustomerRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(new Customers);
    }

    public function getAllClientCode()
    {
        return Customers::select('client_code')
            ->where('active', 1)
            ->pluck('client_code');
    }

    /**
     * @param string $clientCode
     * @return Customers|null
     */
    public function findByClientCode(string $clientCode): ?Customers
    {
        try {
            $customer = $this->model
                ->where('client_code', $clientCode)
                ->first();

        } catch (\Throwable $e) {
            LaravelLoggerUtil::loggerException($e);
            $customer = null;
        }

        return $customer;
    }
}
