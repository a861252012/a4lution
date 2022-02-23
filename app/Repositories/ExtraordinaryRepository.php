<?php

namespace App\Repositories;

use App\Models\ExtraordinaryItem;

class ExtraordinaryRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(new ExtraordinaryItem);
    }

    public function getExtraordinaryItem(
        string $reportDate,
        string $clientCode
    ): float {
        return (float)$this->model->selectRaw('SUM(item_amount) as extraordinary_item')
            ->where('report_date', $reportDate)
            ->where('client_code', $clientCode)
            ->groupBy('client_code', 'report_date')
            ->value('extraordinary_item');
    }
}
