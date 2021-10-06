<?php

namespace App\Imports;

use App\Models\PlatformAdFees;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\RemembersRowNumber;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

class PlatformAdFeesImport implements ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading, WithValidation
{
    use Importable, RegistersEventListeners, RemembersRowNumber;

    private $rows = 0;

    public function model(array $row)
    {
        ++$this->rows;

        return new PlatformAdFees([
            'client_code' => $row['client_code'],
            'client_type' => $row['client_type'],
            'platform' => $row['platform'],
            'account' => $row['account'],
            'campagin_type' => $row['campagin_type'],
            'campagin' => $row['campagin'],
            'currency' => $row['currency'],
            'Impressions' => $row['impressions'],
            'clicks' => $row['clicks'],
            'ctr' => $row['ctr'],
            'spendings' => $row['spendings'],
            'spendings_hkd' => $row['spendings_hkd'],
            'cpc' => $row['cpc'],
            'sales_qty' => $row['sales_qty'],
            'sales_amount' => $row['sales_amount'],
            'sales_amount_hkd' => $row['sales_amount_hkd'],
            'acos' => $row['acos'],
            'exchange_rate' => $row['exchange_rate'],
            'upload_id' => 1,
            'report_date' => date('Y-m-d'),
            'active' => 1,
//            'created_at' => Carbon::now(),
            'created_at' => date('Y-m-d h:i:s'),
            'created_by' => 2,
//            'updated_at' => Carbon::now(),
            'updated_at' => date('Y-m-d h:i:s'),
            'updated_by' => 2,
        ]);
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function getRowCount(): int
    {
        return $this->rows;
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function rules(): array
    {
        return [
            'client_code' => ['bail', 'nullable', 'string', 'max:50'],
            'client_type' => ['bail', 'nullable', 'string', 'max:50'],
            'platform' => ['bail', 'nullable', 'string', 'max:100'],
            'account' => ['bail', 'nullable', 'string', 'max:100'],
            'campagin_type' => ['bail', 'nullable', 'string', 'max:100'],
            'campagin' => ['bail', 'nullable', 'string', 'max:255'],
            'currency' => ['bail', 'nullable', 'string', 'max:50'],
            'Impressions' => ['bail', 'nullable', 'numeric', 'max:9999999999'],
            'clicks' => ['bail', 'nullable', 'numeric', 'max:9999999999'],
            'ctr' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
            'spendings' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
            'spendings_hkd' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
            'cpc' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
            'sales_qty' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
            'sales_amount' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
            'sales_amount_hkd' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
            'acos' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
            'exchange_rate' => ['bail', 'nullable', 'numeric', 'max:999999999999'],

            '*.client_code' => ['bail', 'nullable', 'string', 'max:50'],
            '*.client_type' => ['bail', 'nullable', 'string', 'max:50'],
            '*.platform' => ['bail', 'nullable', 'string', 'max:100'],
            '*.account' => ['bail', 'nullable', 'string', 'max:100'],
            '*.campagin_type' => ['bail', 'nullable', 'string', 'max:100'],
            '*.campagin' => ['bail', 'nullable', 'string', 'max:255'],
            '*.currency' => ['bail', 'nullable', 'string', 'max:50'],
            '*.Impressions' => ['bail', 'nullable', 'numeric', 'max:9999999999'],
            '*.clicks' => ['bail', 'nullable', 'numeric', 'max:9999999999'],
            '*.ctr' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
            '*.spendings' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
            '*.spendings_hkd' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
            '*.cpc' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
            '*.sales_qty' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
            '*.sales_amount' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
            '*.sales_amount_hkd' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
            '*.acos' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
            '*.exchange_rate' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
        ];
    }
}
