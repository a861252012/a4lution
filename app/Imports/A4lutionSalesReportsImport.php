<?php

namespace App\Imports;

use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Imports\A4lutionSalesReports\PlatformAdFeeImport;

class A4lutionSalesReportsImport implements WithMultipleSheets
{
    
    public $rows = 0;
    private $userID;
    private $batchID;
    private $inputReportDate;

    public function sheets(): array
    {
        return [
            'Amz Ads' => new PlatformAdFeeImport(),
            // 'eBay Ads' => new PlatformAdFeeImport(),
        ];
    }

    // // 多個 sheet 也是會一直執行到每行，到此 sheet 沒 row 後，會執行下個 sheet，以此類推。
    // public function onRow(Row $row)
    // {
    //     // $rowIndex = $row->getIndex();
    //     // $row      = $row->toArray();
    //     dd($row->getDelegate());
    //     dump($row->getIndex()); // 從 1 開始
    //     dump($row->toArray());
    // }
}
