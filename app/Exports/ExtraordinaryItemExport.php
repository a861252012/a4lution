<?php

namespace App\Exports;

use App\Models\Invoice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Excel;
use Throwable;

class ExtraordinaryItemExport implements WithTitle, FromQuery, WithHeadings, withMapping, WithStrictNullComparison, WithEvents
{
    use RegistersEventListeners;

    private $reportDate;
    private $clientCode;
    private $insertInvoiceID;

    public function __construct(
        string $reportDate,
        string $clientCode,
        int    $insertInvoiceID
    )
    {
        $this->reportDate = $reportDate;
        $this->clientCode = $clientCode;
        $this->insertInvoiceID = $insertInvoiceID;
    }

    public function title(): string
    {
        return 'Extraordinary item';
    }

    public function failed(Throwable $exception): void
    {
        $invoice = Invoice::findOrFail($this->insertInvoiceID);
        $invoice->doc_status = "deleted";
        $invoice->save();

        \Log::channel('daily_queue_export')
            ->info('ExtraordinaryItemExport')
            ->info($exception);
    }

    public function query()
    {
        return DB::query()
            ->from("extraordinary_items")
            ->select(
                'item_name',
                'description',
                'receivable_amount',
                'payable_amount',
                'item_amount'
            )
            ->where('active', 1)
            ->where('report_date', $this->reportDate)
            ->where('client_code', $this->clientCode)
            ->orderBy('item_name');
    }

    public function headings(): array
    {
        return [
            'Item Name',
            'Description',
            'Receivable',
            'Payable',
            'Total Amount',
        ];
    }

    public function map($row): array
    {
        return [
            [
                $row->item_name,
                $row->description,
                $row->receivable_amount,
                $row->payable_amount,
                $row->item_amount,
            ]
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $invoice = Invoice::findOrFail($this->insertInvoiceID);
                $invoice->doc_status = "active";
                $invoice->save();
            },
        ];
    }
}
