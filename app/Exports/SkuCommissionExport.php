<?php

namespace App\Exports;

use Maatwebsite\Excel\Excel;
use App\Models\CommissionSkuSetting;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\FromCollection;

class SkuCommissionExport implements WithHeadings, FromCollection, Responsable
{
    use Exportable;

    private $fileName;
    private $writerType = Excel::XLSX;

    private $clientCode;
    private $sku;
    
    public function __construct($clientCode = null, $sku = null)
    {
        $this->fileName = sprintf(
            'sku_commission_%s_%s_%s.xlsx',
            $clientCode ?? 'all',
            $sku ?? 'all',
            now()->format('Ymdhis'),
        );

        $this->clientCode = $clientCode;
        $this->sku = $sku;
    }

    public function collection()
    {
        return CommissionSkuSetting::query()
            ->select([
                'site',
                'currency',
                'sku',
                'threshold',
                'basic_rate',
                'upper_bound_rate',
            ])
            ->when($this->clientCode, fn($q, $clientCode) => $q->where('client_code', $clientCode))
            ->when($this->sku, fn($q, $sku) => $q->where('sku', $sku))
            ->active()
            ->latest()
            ->get();
    }

    public function headings(): array
    {
        return [
            'Site',
            'Currency',
            'SKU',
            'Threshold',
            'Basic Rate (<Threshold)',
            'Higher Rate (>=Threshold)',
        ];
    }
}
