<?php

namespace App\Repositories;

use App\Models\CommissionSetting;
use App\Support\LaravelLoggerUtil;

class CommissionSettingRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(new CommissionSetting);
    }

    /**
     * @param string $clientCode
     * @return CommissionSetting|null
     */
    public function findByClientCode(string $clientCode): ?CommissionSetting
    {
        try {
            $commissionSetting = $this->model
                ->where('client_code', $clientCode)
                ->first();

        } catch (\Throwable $e) {
            LaravelLoggerUtil::loggerException($e);
            $commissionSetting = null;
        }

        return $commissionSetting;
    }
}
