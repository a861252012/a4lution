<?php

namespace App\Imports;

use App\Models\BatchJobs;

class ImportHasFailedNotification
{
    private $batchID;

    public function __construct(
        $batchID
    )
    {
        $this->batchID = $batchID;
    }

    public function updateBatchStatus()
    {
        BatchJobs::where('id', '=', $this->batchID)->update(
            ['status' => 'failed'], ['total_count' => '666']
        );
    }

}
