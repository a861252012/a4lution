<?php

namespace App\Repositories;

use App\Models\Invoices;

class InvoiceRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(new Invoices);
    }
}
