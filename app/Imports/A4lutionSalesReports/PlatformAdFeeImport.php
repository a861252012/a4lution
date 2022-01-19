<?php

namespace App\Imports\A4lutionSalesReports;

use Maatwebsite\Excel\Row;
use Illuminate\Support\LazyCollection;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Events\AfterImport;

class PlatformAdFeeImport implements OnEachRow
{
    private $data;

    public function __construct()
    {
        dump('dddd');
        $this->data = LazyCollection::make();
    }

    // 多個 sheet 也是會一直執行到每行，到此 sheet 沒 row 後，會執行下個 sheet，以此類推。
    public function onRow(Row $row)
    {
        $this->data->push($row->toArray());
    }

    public static function afterImport(AfterImport $event)
    {
        $importer = $event->getConcernable();

        dd($importer->data);
        

    }
}
