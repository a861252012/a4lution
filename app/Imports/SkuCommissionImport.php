<?php

namespace App\Imports;

use App\Models\CommissionSkuSetting;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class SkuCommissionImport implements ToModel, WithStartRow, WithValidation, WithHeadingRow
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

    public function rules(): array
    {
        return [
            '3' => ['required','numeric'],
            '4' => ['required','numeric'],
            '5' => ['required','numeric'],
        ];
    }

    public function customValidationAttributes()
    {
        return [
            '3' => '[ Threshold ]',
            '4' => '[ Basic Rate ]',
            '5' => '[ Higher Rate ]',
        ];
    }
}
