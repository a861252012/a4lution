<?php

namespace App\Imports;

use App\Models\CommissionSkuSetting;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;

class SkuCommissionImport implements ToModel
{
    use Importable;

    private string $clientCode;

    public function __construct(string $clientCode) {
        $this->clientCode = $clientCode;
    }

    public function model(array $row)
    {
        return new CommissionSkuSetting([
            'client_code' => $this->clientCode,
            'site' => $row[0],
            'currency' => $row[1],
            'sku' => $row[2],
            'threshold' => $row[3],
            'basic_rate' => $row[4],
            'upper_bound_rate' => $row[5],
        ]);
    }

    public function startRow(): int
    {
        return 2;
    }

}
