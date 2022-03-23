<?php

namespace App\Exports;

use App\Models\ReturnHelperCharge;
use App\Repositories\ContinStorageFeeRepository;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithTitle;
use App\Models\FirstMileShipmentFee;
use Maatwebsite\Excel\Events\BeforeSheet;
use Throwable;

class FBADataExport implements
    WithTitle,
    WithEvents,
    WithStrictNullComparison
{
    private string $reportDate;
    private string $clientCode;
    private int  $serialNumber;

    public function __construct(
        string $reportDate,
        string $clientCode
    ) {
        $this->reportDate = $reportDate;
        $this->clientCode = $clientCode;
    }

    public function failed(Throwable $exception): void
    {
        \Log::channel('daily_queue_export')
            ->info('FBADataExport')
            ->info($exception);
    }


    public function title(): string
    {
        return 'FBA Data';
    }

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function (BeforeSheet $event) {

                //1.)   Contin Storage Fee :  (一筆加總的數值)
                $continStorageFee = app(ContinStorageFeeRepository::class)->getContinStorageFee(
                    $this->reportDate,
                    $this->clientCode
                );

                $event->sheet->SetCellValue("A1", $continStorageFee->item_description);
                $event->sheet->SetCellValue("B1", "$ {$continStorageFee->unit_price}");

                // 2.)  Contin 寄FBA的頭程費用 : 依據shipment
                $firstMileShipmentFeeList = FirstMileShipmentFee::selectRaw("
                    fulfillment_center as 'country',
                    fba_shipment as 'shipment_id',
                    COUNT(DISTINCT ids_sku) AS 'sku',
                    SUM(shipped) as 'shipped_qty',
                    ROUND(total, 2) as 'unit_price'")
                    ->where('active', 1)
                    ->where('report_date', $this->reportDate)
                    ->where('client_code', $this->clientCode)
                    ->groupBy(['fulfillment_center', 'fba_shipment'])
                    ->get();

                if (count($firstMileShipmentFeeList) > 0) {
                    foreach ($firstMileShipmentFeeList as $item) {
                        $this->serialNumber++;
                        $itemDesc = sprintf(
                            "Country:%s, Shipment ID:%s, SKU:%d, Shipped Qty:%d",
                            $item->country,
                            $item->shipment_id,
                            $item->sku,
                            $item->shipped_qty,
                        );

                        $event->sheet->SetCellValue("A{$this->serialNumber}", $itemDesc);
                        $event->sheet->SetCellValue("B{$this->serialNumber}", $this->unit_price);
                    }
                }

                // 3.)  Return Helper : 逐筆列出
                $returnHelperList = ReturnHelperCharge::selectRaw("
                return_helper_charges.notes,
                (return_helper_charges.amount * exchange_rates.exchange_rate) AS 'amount_hkd'")
                    ->leftJoin('exchange_rates', function ($join) {
                        $join->on('return_helper_charges.report_date', '=', 'exchange_rates.quoted_date')
                            ->on('return_helper_charges.currency_code', '=', 'exchange_rates.base_currency')
                            ->where('exchange_rates.active', 1);
                    })
                    ->where('return_helper_charges.supplier', $this->clientCode)
                    ->where('return_helper_charges.report_date', $this->reportDate)
                    ->where('return_helper_charges.active', 1)
                    ->get();

                if (count($returnHelperList) > 0) {
                    foreach ($returnHelperList as $item) {
                        $this->serialNumber++;

                        $event->sheet->SetCellValue("A{$this->serialNumber}", $item->notes);
                        $event->sheet->SetCellValue("B{$this->serialNumber}", $item->amount_hkd);
                    }
                }
            }
        ];
    }
}
