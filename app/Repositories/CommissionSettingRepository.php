<?php

namespace App\Repositories;

use App\Models\CommissionSettings;
use App\Support\LaravelLoggerUtil;

class CommissionSettingRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(new CommissionSettings);
    }

    /**
     * @param string $clientCode
     * @return CommissionSettings|null
     */
    public function findByClientCode(string $clientCode): ?CommissionSettings
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
