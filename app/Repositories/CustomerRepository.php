<?php

namespace App\Repositories;

use App\Models\Customer;
use App\Support\LaravelLoggerUtil;

class CustomerRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(new Customer);
    }

    public function getAllClientCode(bool $isActive = true)
    {
        return $this->model
            ->select('client_code')
            ->when($isActive, fn ($q) => $q->where('active', 1))
            ->pluck('client_code');
    }

    /**
     * @param string $clientCode
     * @return Customer|null
     */
    public function findByClientCode(string $clientCode): ?Customer
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
