<?php

namespace App\Support;

use Illuminate\Support\Str;
use Illuminate\Support\LazyCollection;
use Spatie\SimpleExcel\SimpleExcelReader as SpatieSimpleExcelReader;

class SimpleExcelReader extends SpatieSimpleExcelReader
{
    public function getRowsBySheet($sheet): LazyCollection
    {
        $this->rowIterator = $sheet->getRowIterator();

        $this->rowIterator->rewind();

        /** @var \Box\Spout\Common\Entity\Row $firstRow */
        $firstRow = $this->rowIterator->current();

        if (is_null($firstRow)) {
            $this->noHeaderRow();
        }

        if ($this->processHeader) {
            $this->headers = $this->processHeaderRow($firstRow->toArray());

            $this->rowIterator->next();
        }

        return LazyCollection::make(function () {
            while ($this->rowIterator->valid() && $this->skip && $this->skip--) {
                $this->rowIterator->next();
            }
            while ($this->rowIterator->valid() && (! $this->useLimit || $this->limit--)) {
                $row = $this->rowIterator->current();

                yield $this->getValueFromRow($row);

                $this->rowIterator->next();
            }
        });
    }

    public function getHeadersBySheet($sheet): ?array
    {
        $this->rowIterator = $sheet->getRowIterator();

        $this->rowIterator->rewind();

        /** @var \Box\Spout\Common\Entity\Row $firstRow */
        $firstRow = $this->rowIterator->current();

        if (is_null($firstRow)) {
            $this->noHeaderRow();

            return null;
        }

        return $this->processHeaderRow($firstRow->toArray());
    }


    protected function toSnakeCase(string $header): string
    {
        return Str::slug($header, '_');
    }
}
