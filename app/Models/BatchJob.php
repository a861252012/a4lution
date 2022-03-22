<?php


namespace App\Models;

use App\Models\User;
use App\Models\PlatformAdFee;
use App\Models\WfsStorageFee;
use App\Models\MonthlyStorageFee;
use App\Models\LongTermStorageFee;
use App\Models\ReturnHelperCharge;
use App\Models\FirstMileShipmentFee;
use App\Models\AmazonDateRangeReport;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BatchJob extends Model
{
    protected $table = "batch_jobs";

    protected $guarded = [];

    public $timestamps = false;

    ###################
    ## Relationships ##
    ###################

    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function platformAdFees()
    {
        return $this->hasMany(PlatformAdFee::class, 'upload_id');
    }

    public function amazonDateRangeReports()
    {
        return $this->hasMany(AmazonDateRangeReport::class, 'upload_id');
    }

    public function longTermStorageFees()
    {
        return $this->hasMany(LongTermStorageFee::class, 'upload_id');
    }

    public function monthlyStorageFees()
    {
        return $this->hasMany(MonthlyStorageFee::class, 'upload_id');
    }

    public function firstMileShipmentFees()
    {
        return $this->hasMany(FirstMileShipmentFee::class, 'upload_id');
    }

    public function continStorageFees()
    {
        return $this->hasMany(ContinStorageFee::class, 'upload_id');
    }

    public function returnHelperCharges()
    {
        return $this->hasMany(ReturnHelperCharge::class, 'upload_id');
    }

    public function wfsStorageFees()
    {
        return $this->hasMany(WfsStorageFee::class, 'upload_id');
    }
}
